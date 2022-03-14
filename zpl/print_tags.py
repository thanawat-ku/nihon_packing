import mysql.connector
from zebra import Zebra
printer_id=2
printer_name="ZDesigner GT800 (EPL)"

def print_tag(printer_name,customer_name,part_no,part_name,po_no,inv_no,
        ship_date,order_qty,box_no,total_box,tag_no,qty,first_name):
    z = Zebra()
    z.getqueues()
    z.setqueue(printer_name)
    tag="""
    ^XA
    ^FO15,15^A0,15,15^FDCustomer:^FS
    ^FO15,35^A0,25,25^FD"""+customer_name+"""A^FS
    ^FO10,70^GB780,3,3^FS

    ^FO15,85^A0,15,15^FDPart No:^FS
    ^FO15,105^A0,25,25^FD"""+part_no+"""^FS
    ^FO15,145^A0,15,15^FDPart Name:^FS
    ^FO15,165^A0,25,25^FD"""+part_name+"""^FS

    ^FO500,85^A0,15,15^FDPO No:^FS
    ^FO500,105^A0,25,25^FD"""+po_no+"""^FS
    ^FO500,145^A0,15,15^FDInv No:^FS
    ^FO500,165^A0,25,25^FD"""+inv_no+"""^FS

    ^FO480,70^GB3,125,3^FS
    ^FO10,135^GB780,3,3^FS
    ^FO10,195^GB780,3,3^FS

    ^FO15,205^A0,15,15^FDShipping Date:^FS
    ^FO15,225^A0,25,25^FD"""+str(ship_date)+"""^FS
    ^FO280,195^GB3,55,3^FS
    ^FO300,205^A0,15,15^FDOrder Qty:^FS
    ^FO300,225^A0,25,25^FD"""+str(order_qty)+"""^FS
    ^FO600,205^A0,15,15^FDBox No:^FS
    ^FO600,225^A0,25,25^FD"""+str(box_no)+""" of """+str(total_box)+"""^FS
    ^FO580,195^GB3,205,3^FS
    ^FO10,250^GB780,3,3^FS

    ^FO100,260^BY2^BC,60,,,,A^FD"""+tag_no+"""^FS
    ^FO600,265^A0,15,15^FDQuantity:^FS
    ^FO600,285^A0,60,60^FD"""+str(qty)+"""^FS
    ^FO10,345^GB780,3,3^FS
    ^FO15,360^A0,40,40^FDNIHON SEIKI THAI LTD.^FS
    ^FO600,355^A0,15,15^FDIssue By:^FS
    ^FO600,375^A0,20,20^FD"""+first_name+"""^FS
    ^XZ
    """

    print(tag)
    #z.autosense()
    #z.setup( direct_thermal=None, label_height=None, label_width=609 )
    
    z.output(tag)

mydb = mysql.connector.connect(
  host="192.168.10.10",
  user="root",
  password="qctest123",
  database="packing"
)
mycursor = mydb.cursor()

#print_tag(printer_name,customer_name,part_no,part_name,po_no,inv_no,
#ship_date,order_qty,box_no,total_box,qty,first_name)
mycursor.execute("SELECT T.id,C.customer_name,P.part_no,P.part_name,S.sell_no,S.invoice_no, \
    S.sell_date,S.total_qty,T.box_no,T.total_box,T.tag_no,T.quantity,U1.first_name \
    FROM tags T \
    INNER JOIN sells S ON T.sell_id=S.id \
    INNER JOIN products P ON S.product_id=P.id \
    INNER JOIN customers C ON P.customer_id=C.id \
    LEFT OUTER JOIN users U1 ON T.created_user_id=U1.id \
    WHERE T.wait_print='Y' AND T.printer_id="+str(printer_id)+" \
    ORDER BY T.id")
myresult = mycursor.fetchall()
for t in myresult:
    print(t)
    print_tag(printer_name,t[1],t[2],t[3],t[4],t[5],t[6],t[7],t[8],t[9],t[10],t[11],t[12])
    #mycursor.execute("UPDATE tags SET wait_print='N' WHERE id="+str(t[0]))

mydb.commit()
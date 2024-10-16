import mysql.connector
from zebra import Zebra
import time
tag_printer="AY-Tag"

def print_tag(printer_name,customer_name,part_no,part_name,po_no,
        ship_date,box_no,total_box,tag_no,qty):
    z = Zebra()
    z.getqueues()
    z.setqueue(printer_name)
    tag="""
^XA
^FO35,15^A0,15,15^FDCustomer:^FS
^FO35,35^A0,20,20^FD"""+customer_name+"""^FS
^FO520,15^A0,15,15^FDShipping Date:^FS
^FO520,35^A0,25,25^FD"""+ship_date+"""^FS
^FO30,70^GB780,3,3^FS

^FO500,5^GB3,370,3^FS

^FO35,85^A0,15,15^FDPart No:^FS
^FO50,100^BY2^BC,60,,,,A^FD"""+part_no+"""^FS
^FO520,85^A0,15,15^FDQuantity:^FS
^FO550,100^BY2^BC,60,,,,A^FD"""+qty+"""^FS
^FO30,190^GB780,3,3^FS

^FO35,200^A0,15,15^FDPart Name:^FS
^FO35,220^A0,25,25^FD"""+part_name+"""^FS
^FO520,200^A0,15,15^FDP/O No:^FS
^FO520,220^A0,25,25^FD"""+po_no+"""^FS
^FO30,250^GB780,3,3^FS

^FO35,260^A0,15,15^FDSerial No:^FS
^FO50,280^BY2^BC,60,,,,A^FD"""+tag_no+"""^FS
^FO520,260^A0,15,15^FDPackage Count:^FS

^FO520,290^A0,60,60^FD""" + box_no + """ / """ + total_box + """^FS
^FO30,370^GB780,3,3^FS
^FO35,380^A0,20,20^FDNIHON SEIKI THAI LTD. Made in Thailand^FS
^XZ"""
    print(tag)
    #z.autosense()
    #z.setup( direct_thermal=None, label_height=None, label_width=609 )
    
    z.output(tag)

mydb = mysql.connector.connect(
  host="mis.nihonseikithai.co.th",
  user="root",
  password="qctest123",
  database="packing"
)
mycursor = mydb.cursor()

#print_tag(printer_name,customer_name,part_no,part_name,po_no,inv_no,
#ship_date,order_qty,box_no,total_box,qty,first_name)
mycursor.execute("SELECT T.id,C.customer_name,P.part_no,P.part_name,S.pack_no, \
    S.pack_date,T.box_no,T.total_box,T.tag_no,T.quantity \
    FROM tags T \
    STRAIGHT_JOIN packs S ON T.pack_id=S.id \
    STRAIGHT_JOIN products P ON S.product_id=P.id \
    STRAIGHT_JOIN customers C ON P.customer_id=C.id \
    STRAIGHT_JOIN printers PT ON T.printer_id=PT.id \
    WHERE T.wait_print='Y' AND PT.printer_name="+str(tag_printer)+" \
    ORDER BY T.id")
myresult = mycursor.fetchall()
i=0
for t in myresult:
    print(t)
    print_tag(tag_printer,t[1],t[2],t[3],t[4],t[5],t[6],t[7],t[8],t[9])
    mycursor.execute("UPDATE tags SET wait_print='N' WHERE id="+str(t[0]))
    i=i+1
    if i%5==4:
        time.sleep(1.0)

mydb.commit()
import mysql.connector
from zebra import Zebra
printer_id=1
printer_name="ZDesigner GT800 (EPL)"

def print_label(printer_name,part_no,part_name,lot_no,qty,label_no,visual_by,pack_by):
    #z = Zebra()
    #z.getqueues()
    #z.setqueue(printer_name)
    label="^XA^FO10,15^A0,15,15^FDPartNo:^FS^FO10,35^A0,20,20^FD"+part_no+"^FS\n"
    label+="^FO10,55^GB320,3,3^FS^FO10,65^A0,15,15^FDPartName:^FS^FO10,85^A0,20,20^FD"+part_name+"^FS\n"
    label+="^FO10,105^GB320,3,3^FS^FO10,115^A0,15,15^FDLotNo:^FS^FO10,135^A0,20,20^FD"+lot_no+"^FS\n"
    label+="^FO330,65^GB270,3,3^FS^FO345,15^A0,15,15^FDQuantity:^FS^FO410,15^A0,60,60^FD"+str(qty)+"^FS\n"
    label+="^FO330,15^GB5,180,3^FS^FO345,75^BY2^BC,60,,,,A^FD"+label_no+"^FS\n"
    label+="^FO330,160^GB270,3,3^FS^FO10,155^GB320,3,3^FS\n"
    label+="^FO10,165^A0,15,15^FDVisual By:^FS^FO10,180^A0,20,20^FD"+visual_by+"^FS\n"
    label+="^FO160,155^GB3,40,3^FS\n"
    label+="^FO170,165^A0,15,15^FDPack By:^FS^FO170,180^A0,20,20^FD"+str(pack_by)+"^FS\n"
    label+="^FO350,170^A0,25,25^FDNIHON SEIKI THAI LTD.^FS^XZ\n\n"

    print(label)
    #z.autosense()
    #z.setup( direct_thermal=None, label_height=None, label_width=609 )
    
    #z.output(label)

mydb = mysql.connector.connect(
  host="192.168.10.10",
  user="root",
  password="qctest123",
  database="packing"
)
mycursor = mydb.cursor()

#print_label(printer_name,part_no,part_name,lot_no,qty,label_no,visual_by,pack_by)
mycursor.execute("SELECT L.id,P.part_no,P.part_name,LT.lot_no,L.quantity,L.label_no,U1.first_name,'' AS pack_by \
    FROM labels L \
    INNER JOIN lots LT ON L.prefer_lot_id=LT.id \
    INNER JOIN products P ON LT.product_id=P.id \
    LEFT OUTER JOIN users U1 ON LT.packed_user_id=U1.id \
    WHERE L.wait_print='Y' AND L.printer_id=+"+printer_id+" \
    ORDER BY L.id")
myresult = mycursor.fetchall()
for l in myresult:
    print(l)
    print_label(printer_name,l[1],l[2],l[3],l[4],l[5],l[6],l[7])
    mycursor.execute("UPDATE labels SET wait_print='N' WHERE id="+str(l[0]))

mydb.commit()
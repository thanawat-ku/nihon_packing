import mysql.connector
from zebra import Zebra

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

mycursor.execute("SELECT L.id,L.generate_lot_no, U.username AS print_by,U1.username AS pack_by, \
    P.part_no,P.part_name \
    FROM lots L \
    INNER JOIN products P ON L.product_id=P.id \
    LEFT OUTER JOIN users U ON L.printed_user_id=U.id \
    LEFT OUTER JOIN users U1 ON L.packed_user_id=U1.id \
    WHERE status='CONFIRMED'")

myresult = mycursor.fetchall()

for x in myresult:
    print(x)
    mycursor.execute("SELECT * FROM labels WHERE lot_id="+str(x[0]))
    myresult1 = mycursor.fetchall()
    for y in myresult1:
        print(y)
        print_label('ZDesigner GT800 (EPL)',x[4],x[5],x[1],y[7],y[5],x[2],x[3])
        mycursor.execute("UPDATE labels SET status='PRINTED' WHERE id="+str(y[0]))
    mycursor.execute("UPDATE lots SET status='PRINTED' WHERE id="+str(x[0]))

mydb.commit()
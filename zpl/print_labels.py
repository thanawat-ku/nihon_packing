import mysql.connector
import time
from zebra import Zebra
printer_id=1
printer_name="ZDesigner GT800 (EPL)"

def print_label(printer_name,part_no,part_name,lot_no,qty,label_no,visual_by,pack_by):
    z = Zebra()
    z.getqueues()
    z.setqueue(printer_name)
    label="""
^XA
^FO15,10^A0,15,15^FDPartNo:^FS
^FO15,30^A0,20,20^FD"""+part_no+"""^FS
^FO15,50^GB280,3,3^FS
^FO15,60^A0,15,15^FDPartName:^FS
^FO15,80^A0,20,20^FD"""+part_name+"""^FS
^FO15,100^GB280,3,3^FS
^FO15,110^A0,15,15^FDLotNo:^FS
^FO15,130^A0,20,20^FD"""+lot_no+"""^FS
^FO290,60^GB270,3,3^FS
^FO305,10^A0,15,15^FDQuantity:^FS
^FO380,10^A0,60,60^FD"""+qty+"""^FS
^FO290,10^GB5,180,3^FS
^FO305,70^BY2^BC,60,,,,A^FD"""+label_no+"""^FS
^FO290,155^GB270,3,3^FS
^FO15,150^GB280,3,3^FS
^FO15,160^A0,15,15^FDVisual By:^FS
^FO15,175^A0,20,20^FD"""+visual_by+"""^FS
^FO155,150^GB3,40,3^FS
^FO165,160^A0,15,15^FDPack By:^FS
^FO165,175^A0,20,20^FD"""+pack_by+"""^FS
^FO310,165^A0,25,25^FDNIHON SEIKI THAI LTD.^FS
^XZ"""
    print(label)
    #z.autosense()
    #z.setup( direct_thermal=None, label_height=None, label_width=609 )
    
    z.output(label)

mydb = mysql.connector.connect(
  host="192.168.10.10",
  user="root",
  password="qctest123",
  database="packing"
)
mycursor = mydb.cursor()

#print_label(printer_name,part_no,part_name,lot_no,qty,label_no,visual_by,pack_by)
mycursor.execute("SELECT L.id,P.part_no,P.part_name,LT.generate_lot_no,L.quantity,L.label_no,U1.first_name,'' AS pack_by \
    FROM labels L \
    INNER JOIN lots LT ON L.prefer_lot_id=LT.id \
    INNER JOIN products P ON LT.product_id=P.id \
    LEFT OUTER JOIN users U1 ON LT.packed_user_id=U1.id \
    WHERE L.wait_print='Y' AND L.printer_id=+"+str(printer_id)+" \
    ORDER BY L.id")
myresult = mycursor.fetchall()
i=0
for l in myresult:
    print(l)
    print_label(printer_name,l[1],l[2],l[3],l[4],l[5],"","")
    mycursor.execute("UPDATE labels SET wait_print='N' WHERE id="+str(l[0]))
    i=i+1
    if i%5==4:
        time.sleep(1.0)
#print_label(printer_name,'SQA11 11401','PLUNGER','22310SO09A',186,'P00000000002','Yae',"")
mydb.commit()
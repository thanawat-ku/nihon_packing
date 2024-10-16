import mysql.connector
import time
from zebra import Zebra
printer_name="AY-Label"

def print_label(printer_name,part_no,part_name,lot_no,qty,label_no,visual_by,pack_by):
    z = Zebra()
    z.getqueues()
    z.setqueue(printer_name)
    label="""
^XA
^FO145,10^A0,15,15^FDPartNo:^FS
^FO145,30^A0,20,20^FD"""+part_no+"""^FS
^FO145,50^GB280,3,3^FS
^FO145,60^A0,15,15^FDPartName:^FS
^FO145,80^A0,20,20^FD"""+part_name+"""^FS
^FO145,100^GB280,3,3^FS
^FO145,110^A0,15,15^FDLotNo:^FS
^FO145,130^A0,20,20^FD"""+lot_no+"""^FS
^FO420,60^GB270,3,3^FS
^FO435,10^A0,15,15^FDQuantity:^FS
^FO510,10^A0,60,60^FD"""+qty+"""^FS
^FO420,10^GB5,180,3^FS
^FO435,70^BY2^BC,60,,,,A^FD"""+label_no+"""^FS
^FO420,155^GB270,3,3^FS
^FO145,150^GB280,3,3^FS
^FO145,160^A0,15,15^FDVisual By:^FS
^FO145,175^A0,20,20^FD"""+visual_by+"""^FS
^FO285,150^GB3,40,3^FS
^FO295,160^A0,15,15^FDPack By:^FS
^FO295,175^A0,20,20^FD"""+pack_by+"""^FS
^FO440,165^A0,25,25^FDNIHON SEIKI THAI LTD.^FS
^XZ"""
    print(label)
    #z.autosense()
    #z.setup( direct_thermal=None, label_height=None, label_width=609 )
    
    z.output(label)

mydb = mysql.connector.connect(
  host="mis.nihonseikithai.co.th",
  user="root",
  password="qctest123",
  database="packing"
)
mycursor = mydb.cursor()

#print_label(printer_name,part_no,part_name,lot_no,qty,label_no,visual_by,pack_by)
mycursor.execute("SELECT L.id,P.part_no,P.part_name,LT.generate_lot_no,L.quantity,L.label_no,U1.first_name,'' AS pack_by \
    FROM labels L \
    STRAIGHT_JOIN lots LT ON L.prefer_lot_id=LT.id \
    STRAIGHT_JOIN products P ON LT.product_id=P.id \
    STRAIGHT_JOIN printers PT ON L.printer_id=PT.id \
    LEFT OUTER JOIN users U1 ON LT.packed_user_id=U1.id \
    WHERE L.wait_print='Y' AND PT.printer_name="+str(printer_name)+" \
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
from zebra import Zebra
from time import sleep
z = Zebra()
z.getqueues()
z.setqueue('ZDesigner GT800 (EPL)')
for i in range(1,2):
    label="""
^XA
^FO145,10^A0,15,15^FDPartNo:^FS
^FO145,30^A0,20,20^FD3511-RKG0-A000^FS
^FO145,50^GB280,3,3^FS
^FO145,60^A0,15,15^FDPartName:^FS
^FO145,80^A0,20,20^FDDUMMY SHAFT^FS
^FO145,100^GB280,3,3^FS
^FO145,110^A0,15,15^FDLotNo:^FS
^FO145,130^A0,20,20^FDL00000000002^FS
^FO420,60^GB270,3,3^FS
^FO435,10^A0,15,15^FDQuantity:^FS
^FO510,10^A0,60,60^FD180^FS
^FO420,10^GB5,180,3^FS
^FO435,70^BY2^BC,60,,,,A^FDP0000000000"""+str(i)+"""^FS
^FO420,155^GB270,3,3^FS
^FO145,150^GB280,3,3^FS
^FO145,160^A0,15,15^FDVisual By:^FS
^FO145,175^A0,20,20^FDKanokwan^FS
^FO285,150^GB3,40,3^FS
^FO295,160^A0,15,15^FDPack By:^FS
^FO295,175^A0,20,20^FDKanokwan^FS
^FO440,165^A0,25,25^FDNIHON SEIKI THAI LTD.^FS
^XZ"""
    #z.autosense()
    #z.setup( direct_thermal=None, label_height=None, label_width=609 )
    z.output(label)
    sleep(1)

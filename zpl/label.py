from zebra import Zebra
from time import sleep
z = Zebra()
z.getqueues()
z.setqueue('ZDesigner GT800 (EPL)')
for i in range(1):
    label="""
^XA
^FO15,10^A0,15,15^FDPartNo:^FS
^FO15,30^A0,20,20^FD3511-RR80-0016^FS
^FO15,50^GB280,3,3^FS
^FO15,60^A0,15,15^FDPartName:^FS
^FO15,80^A0,20,20^FDSHAFT, THROTTLE^FS
^FO15,100^GB280,3,3^FS
^FO15,110^A0,15,15^FDLotNo:^FS
^FO15,130^A0,20,20^FDL00010001002^FS
^FO290,60^GB270,3,3^FS
^FO305,10^A0,15,15^FDQuantity:^FS
^FO380,10^A0,60,60^FD10^FS
^FO290,10^GB5,180,3^FS
^FO305,70^BY2^BC,60,,,,A^FDP00000000012^FS
^FO290,155^GB270,3,3^FS
^FO15,150^GB280,3,3^FS
^FO15,160^A0,15,15^FDVisual By:^FS
^FO15,175^A0,20,20^FDThanawat^FS
^FO155,150^GB3,40,3^FS
^FO165,160^A0,15,15^FDPack By:^FS
^FO165,175^A0,20,20^FDThanawat^FS
^FO310,165^A0,25,25^FDNIHON SEIKI THAI LTD.^FS
^XZ"""
    #z.autosense()
    #z.setup( direct_thermal=None, label_height=None, label_width=609 )
    z.output(label)
    sleep(1)
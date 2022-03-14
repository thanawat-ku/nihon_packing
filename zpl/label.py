from zebra import Zebra
from time import sleep
z = Zebra()
z.getqueues()
z.setqueue('ZDesigner GT800 (EPL)')
for i in range(1):
    label="""
    ^XA
    ^FO125,15^A0,15,15^FDPartNo:^FS
    ^FO125,35^A0,20,20^FD2CM-F3113-00-00-80 7751 9422^FS
    ^FO125,55^GB320,3,3^FS
    ^FO125,65^A0,15,15^FDPartName:^FS
    ^FO125,85^A0,20,20^FDCS STOPPER LEVER SHAFT B94(JQ76)^FS
    ^FO125,105^GB320,3,3^FS
    ^FO125,115^A0,15,15^FDLotNo:^FS
    ^FO125,135^A0,20,20^FD21503K20R10B^FS
    ^FO445,65^GB270,3,3^FS
    ^FO460,15^A0,15,15^FDQuantity:^FS
    ^FO535,15^A0,60,60^FD20,000^FS
    ^FO445,15^GB5,180,3^FS
    ^FO460,75^BY2^BC,60,,,,A^FDP"""+str(i+21).zfill(11)+"""^FS
    ^FO445,160^GB270,3,3^FS
    ^FO125,155^GB320,3,3^FS
    ^FO125,165^A0,15,15^FDVisual By:^FS
    ^FO125,180^A0,20,20^FDthanawat^FS
    ^FO275,155^GB3,40,3^FS
    ^FO285,165^A0,15,15^FDPack By:^FS
    ^FO465,170^A0,25,25^FDNIHON SEIKI THAI LTD.^FS
    ^XZ
    """
    #z.autosense()
    #z.setup( direct_thermal=None, label_height=None, label_width=609 )
    z.output(label)
    sleep(1)
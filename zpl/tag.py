from zebra import Zebra
import time
z = Zebra()
z.getqueues()
z.setqueue('ZDesigner GT800 (EPL)')
for i in range(2,31):
    tag="""
    ^XA
    ^FO35,15^A0,15,15^FDCustomer:^FS
    ^FO35,35^A0,20,20^FDHITACHI ASTEMO INDIANA, INC.^FS
    ^FO520,15^A0,15,15^FDShipping Date:^FS
    ^FO520,35^A0,25,25^FD08 Apr 2022^FS
    ^FO30,70^GB780,3,3^FS

    ^FO500,5^GB3,370,3^FS

    ^FO35,85^A0,15,15^FDPart No:^FS
    ^FO50,100^BY2^BC,60,,,,A^FD3511-RKG0-A000^FS
    ^FO520,85^A0,15,15^FDQuantity:^FS
    ^FO550,100^BY2^BC,60,,,,A^FD180^FS
    ^FO30,190^GB780,3,3^FS

    ^FO35,200^A0,15,15^FDPart Name:^FS
    ^FO35,220^A0,25,25^FDDUMMY SHAFT^FS
    ^FO520,200^A0,15,15^FDP/O No:^FS
    ^FO520,220^A0,25,25^FD1991^FS
    ^FO30,250^GB780,3,3^FS

    ^FO35,260^A0,15,15^FDSerial No:^FS
    ^FO50,280^BY2^BC,60,,,,A^FDPT000000000"""+str(i+30)+"""^FS
    ^FO520,260^A0,15,15^FDPackage Count:^FS
    ^FO520,290^A0,60,60^FD"""+str(i)+"""/30^FS

    ^FO30,370^GB780,3,3^FS

    ^FO35,380^A0,20,20^FDNIHON SEIKI THAI LTD. Made in Thailand^FS
    ^XZ
    """
    #z.autosense()
    #z.setup( direct_thermal=None, label_height=None, label_width=812 )
    z.output(tag)
    time.sleep(1.5)

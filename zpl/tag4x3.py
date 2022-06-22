from zebra import Zebra
import time
z = Zebra()
z.getqueues()
z.setqueue('ZDesigner GT800 (EPL)')
for i in range(2,31):
    tag="""
    ^XA

    ^CF0,20
    ^FO50,30^FDPart No^FS
    ^FO50,60^FD(P) 3511-PC2T-0000^FS
    ^FO100,82^BY2^BCN,40,N^FDP3511-PC2T-0000^FS
    ^FO50,135^FDDESC SHAFT,THROTTLE^FS
    ^FO50,160^FDQUANTITY^FS
    ^FO50,180^FD(Q) 10,000^FS
    ^FO100,202^BCN,40,N^FDQ10000^FS
    ^FO50,270^FDSupplier^FS
    ^FO50,300^FD(V) A8130^FS
    ^FO100,322^BCN,40,N^FDVA8130^FS
    ^FO50,400^FDLot No.^FS
    ^FO50,435^FD(1T)^FS
    ^FO100,452^BCN,40,Y,Y^FD1TL000000000001^FS
    ^FO50,500^FDSerial Number^FS
    ^FO50,535^FD(S)^FS
    ^FO100,552^BCN,40,Y,Y^FDST000000000001^FS
    ^FO550,500^FDCOO^FS
    ^CF0,80
    ^FO600,530^FDTH^FS

    ^FO520,50
    ^BQN,2,7
    ^FDQA,P3511-PC2T-0000;Q10000;VA8130;1TL00000000001;ST00000000001^FS
    ^CF0,45
    ^FO340,350^FDNIHON SEIKI THAI LTD^FS

    ^XZ
    """
    #z.autosense()
    #z.setup( direct_thermal=None, label_height=None, label_width=812 )
    z.output(tag)
    time.sleep(1.5)

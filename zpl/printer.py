from cProfile import label
from zebra import Zebra
class Printer:

    def __init__(self,printer_name):
        self.z = Zebra()
        self.z.getqueues()
        self.z.setqueue(printer_name)

    def print_tag(self,customer_name,part_no,part_name,po_no,
            ship_date,box_no,total_box,tag_no,qty):
        
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
        self.z.output(tag)

    def print_hitachi_label(self,part_no,part_name,lot_no,qty,label_no,visual_by,pack_by):
        
        label="""
    ^XA

    ^CF0,20
    ^FO50,30^FDPart No^FS
    ^FO50,60^FD(P) """+part_no+"""^FS
    ^FO100,82^BY2^BCN,40,N^FDP"""+part_no+"""^FS
    ^FO50,135^FDDESC """+part_name+"""^FS
    ^FO50,160^FDQUANTITY^FS
    ^FO50,180^FD(Q) """+qty+"""^FS
    ^FO100,202^BCN,40,N^FDQ"""+qty+"""^FS
    ^FO50,270^FDSupplier^FS
    ^FO50,300^FD(V) A8130^FS
    ^FO100,322^BCN,40,N^FDVA8130^FS
    ^FO50,400^FDLot No.^FS
    ^FO50,435^FD(1T)^FS
    ^FO100,452^BCN,40,Y,Y^FD1T"""+lot_no+"""^FS
    ^FO50,500^FDSerial Number^FS
    ^FO50,535^FD(S)^FS
    ^FO100,552^BCN,40,Y,Y^FDS"""+label_no+"""^FS
    ^FO550,500^FDCOO^FS
    ^CF0,80
    ^FO600,530^FDTH^FS

    ^FO520,50
    ^BQN,2,7
    ^FDQA,P"""+part_no+""";Q"""+qty+""";VA8130;1TL00000000001;S"""+label_no+"""^FS
    ^CF0,45
    ^FO340,350^FDNIHON SEIKI THAI LTD^FS

    ^XZ
    """
        print(label)
        self.z.output(label)

    def print_label(self,part_no,part_name,lot_no,qty,label_no,visual_by,pack_by):
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
        self.z.output(label)

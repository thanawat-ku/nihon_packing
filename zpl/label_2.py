from zebra import Zebra
from time import sleep
z = Zebra()
z.getqueues()
z.setqueue('ZDesigner GT800 (EPL)')
for i in range(1,2):
    label="""
^XA
^FO20,20
^A0,20,20^FDPart No : 3P412927-1A^FS^FO20,50
^A0,20,20^FDName : Tenma 0001^FS

^FO20,80
^A0,20,20^FDQty : 400^FS

^FO20,110
^A0,20,20^FDDate : 22/11/2022^FS

^FO200,110
^A0,20,20^FDPO : J1655555^FS

^FO20,140
^A0,20,20^FDCustomer : TENMA (THAILAND) CO.,LTD^FS

^FO20,170
^A0,20,20^FDSupplier : NIHON SEIKI THAI^FS

^FO480,10
^BQN,2,4
^FD3P412927-1A 400 XXX 190600001^FS

^XZ"""
    #z.autosense()
    #z.setup( direct_thermal=None, label_height=None, label_width=609 )
    z.output(label)
    sleep(1)
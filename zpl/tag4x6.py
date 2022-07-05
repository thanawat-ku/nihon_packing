from zebra import Zebra
import time
z = Zebra()
z.getqueues()
z.setqueue('ZDesigner GT800 (EPL)')
for i in range(2,31):
    tag="""
    ^XA
^BY3,2,70

^FO15,10^GB1190,790,3^FS

^CF0,60
^FO420,30^FDMASTER LABEL^FS
^CF0,30
^FO15,100^GB1190,3,3^FS
^FO35,110^FDF^FS
^FO35,145^FDR^FS
^FO35,180^FDO^FS
^FO35,215^FDM^FS
^FO70,100^GB3,150,3^FS
^FO360,100^GB3,150,3^FS
^FO380,145^FDT^FS
^FO380,180^FDO^FS
^FO415,100^GB3,150,3^FS
^FO820,100^GB3,320,3^FS

^CF0,18
^FO90,110^FB280,4,18,L,0
^FDNIHON SEIKI THAI LIMITED\&
1/77 MOO 5 T.KANHAM A.U-THAI\&
PRANAKORN SRIAYUTTHAYA\&
THAILAND 13210^FS

^FO425,110^FB400,4,18,L,0
^FDHITACHI ASTEMO SAN LUIS POTOSI S.A. de C.V.\&
SANTIAGO PONIETNTE 200, LOTE 01 MANZANA 05\&
SAN LUIS POTOSI\&
MX 78423  MEXICO^FS

^FO830,110^FDPO# (K)^FS
^FO900,140^BC^FD2390^FS

^FO15,250^GB1190,3,3^FS
^FO30,270^FDPC# (P)^FS
^FO100,300^BC^FD3511-PC2TP0000^FS

^CF0,18
^FO830,270^FDDESCRIPTION^FS
^CF0,30
^FO850,300^FDSHAFT, THROTTLE^FS
^FO850,350^FDNIHON SEIKI THAI LTD.^FS

^CF0,18
^FO30,440^FDQTY (Q)^FS
^FO100,480^BC^FD7200^FS
^FO400,420^GB3,180,3^FS
^FO420,440^FDSUPPLIER (V)^FS
^FO460,480^BC^FDA8130 P01USD^FS
^FO1000,420^GB3,180,3^FS
^FO15,420^GB1190,3,3^FS

^FO30,620^FDSERIAL NUMBER (S)^FS
^FO100,670^BC^FDABCDEQ2345^FS
^FO750,600^GB3,200,3^FS
^CF0,18
^FO770,620^FDSHIP DATE^FS
^CF0,30
^FO770,650^FD07-JUL-2022^FS
^FO770,750^FDCOO:THAILAND^FS
^FO15,600^GB1190,3,3^FS
^XZ
    """
    #z.autosense()
    #z.setup( direct_thermal=None, label_height=None, label_width=812 )
    z.output(tag)
    time.sleep(1.5)

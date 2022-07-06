import sys
# for pyside6
#from PySide6.QtWidgets import (
#    QMainWindow, QApplication, QWidget, QLabel, QComboBox, QPushButton, QVBoxLayout, QHBoxLayout
#)
#from PySide6.QtCore import QTimer

# for pyside
from PySide.QtGui import (
    QMainWindow, QApplication, QWidget, QLabel, QComboBox, QPushButton, QVBoxLayout, QHBoxLayout
)
from PySide.QtCore import QTimer

import mysql.connector
import time
from printer import Printer

class MainWindow(QMainWindow):

    def __init__(self):
        super(MainWindow, self).__init__()

        self.setWindowTitle("Print")

        self.label_printer_name="AY-Label"
        self.tag_printer_name="AY-Tag"

        self.combo1 = QComboBox()
        self.combo1.addItems(["AY-Label", "KK-Label", "HAN-Label"])
        self.combo1.setCurrentIndex(self.combo1.findText(self.label_printer_name))
        self.combo2 = QComboBox()
        self.combo2.addItems(["AY-Tag", "KK-Tag"])
        self.combo2.setCurrentIndex(self.combo2.findText(self.tag_printer_name))
        self.set_printer()

    def set_printer(self):
        if self.combo1 is not None:
            self.label_printer_name=self.combo1.currentText()
            self.tag_printer_name=self.combo2.currentText()

        print("Set Config!")

        self.label_printer = str(self.combo1.currentText())
        self.tag_printer = str(self.combo2.currentText())
        self.label_printer = Printer(self.label_printer_name)
        self.tag_printer = Printer(self.tag_printer_name)

        layout2 = QVBoxLayout()
        row1 =QHBoxLayout()
        self.label1=QLabel("Label Printer = "+self.label_printer_name)
        row1.addWidget(self.label1)
        row2 =QHBoxLayout()
        self.label2=QLabel("Tag Printer = "+self.tag_printer_name)
        row2.addWidget(self.label2)
        row3 =QHBoxLayout()
        button2=QPushButton("Config")
        button2.clicked.connect(self.set_config)

        row3.addWidget(button2)
        layout2.addLayout(row1)
        layout2.addLayout(row2)
        layout2.addLayout(row3)

        widget = QWidget()
        widget.setLayout(layout2)
        self.setCentralWidget(widget)
        
        self.isSetting=False
        QTimer.singleShot(2000, self.print_label_tag)

    def set_config(self):
        print("Set Printer!")
        self.isSetting=True

        layout1 = QVBoxLayout()
        row1 =QHBoxLayout()

        label1 = QLabel("Label Printer:")
        self.combo1 = QComboBox()
        self.combo1.addItems(["AY-Label", "KK-Label", "HAN-Label"])
        self.combo1.setCurrentIndex(self.combo1.findText(self.label_printer_name))

        row1.addWidget(label1)
        row1.addWidget(self.combo1)
        
        row2=QHBoxLayout()
        label2 = QLabel("Tag Printer:")
        self.combo2 = QComboBox()
        self.combo2.addItems(["AY-Tag", "KK-Tag"])
        self.combo2.setCurrentIndex(self.combo2.findText(self.tag_printer_name))

        row2.addWidget(label2)
        row2.addWidget(self.combo2)

        row3=QHBoxLayout()
        button1=QPushButton("Set Printer")
        button1.clicked.connect(self.set_printer)

        row3.addWidget(button1)

        layout1.addLayout(row1)
        layout1.addLayout(row2) 
        layout1.addLayout(row3) 

        widget = QWidget()
        widget.setLayout(layout1)
        self.setCentralWidget(widget)

    def print_label_tag(self):
        if self.isSetting==True:
            return
        mydb = mysql.connector.connect(
        host="192.168.10.10",
        user="root",
        password="qctest123",
        database="packing"
        )
        mycursor = mydb.cursor()
        
        if self.label_printer_name!="HAN-Label":
            #for label
            mycursor.execute("SELECT L.id,P.part_no,P.part_name,LT.generate_lot_no,L.quantity,L.label_no,U1.first_name,'' AS pack_by \
            FROM labels L \
            INNER JOIN lots LT ON L.prefer_lot_id=LT.id \
            INNER JOIN products P ON LT.product_id=P.id \
            INNER JOIN printers PT ON L.printer_id=PT.id \
            LEFT OUTER JOIN users U1 ON LT.packed_user_id=U1.id \
            WHERE L.wait_print='Y' AND PT.printer_name=+'"+str(self.label_printer_name)+"' \
            ORDER BY L.id")
            myresult = mycursor.fetchall()
            i=0
            for l in myresult:
                print(l)
                self.label_printer.print_label(l[1],l[2],l[3],str(l[4]),l[5],"","")
                mycursor.execute("UPDATE labels SET wait_print='N' WHERE id="+str(l[0]))
                i=i+1
                if i%5==4:
                    time.sleep(0.5)
            mydb.commit()
        elif self.label_printer_name=="HAN-Label":
            #for HAN label
            mycursor.execute("SELECT L.id,P.part_no,P.part_name,LT.generate_lot_no,L.quantity,L.label_no,U1.first_name,'' AS pack_by \
            FROM labels L \
            INNER JOIN lots LT ON L.prefer_lot_id=LT.id \
            INNER JOIN products P ON LT.product_id=P.id \
            INNER JOIN printers PT ON L.printer_id=PT.id \
            LEFT OUTER JOIN users U1 ON LT.packed_user_id=U1.id \
            WHERE L.wait_print='Y' AND PT.printer_name=+'"+str(self.label_printer_name)+"' \
            ORDER BY L.id")
            myresult = mycursor.fetchall()
            i=0
            for l in myresult:
                print(l)
                self.label_printer.print_hitachi_label(l[1],l[2],l[3],str(l[4]),l[5],"","")
                mycursor.execute("UPDATE labels SET wait_print='N' WHERE id="+str(l[0]))
                i=i+1
                if i%5==4:
                    time.sleep(0.5)
            mydb.commit()

        #for tag
        if self.tag_printer_name=="AY-Tag":
            mycursor.execute("SELECT T.id,C.customer_name,P.part_no,P.part_name,S.pack_no, \
                S.pack_date,T.box_no,T.total_box,T.tag_no,T.quantity \
                FROM tags T \
                INNER JOIN packs S ON T.pack_id=S.id \
                INNER JOIN products P ON S.product_id=P.id \
                INNER JOIN customers C ON P.customer_id=C.id \
                INNER JOIN printers PT ON T.printer_id=PT.id \
                WHERE T.wait_print='Y' AND PT.printer_name='"+str(self.tag_printer_name)+"' \
                ORDER BY T.id")
            myresult = mycursor.fetchall()
            i=0
            for t in myresult:
                print(t)
                self.tag_printer.print_hitachi_tag(t[1],t[2],t[3],t[4],t[5].strftime("%d %b %Y"),str(t[6]),str(t[7]),str(t[8]),str(t[9]))
                mycursor.execute("UPDATE tags SET wait_print='N' WHERE id="+str(t[0]))
                i=i+1
                if i%5==4:
                    time.sleep(0.5)

            mydb.commit()
        else:
            mycursor.execute("SELECT T.id,C.customer_name,P.part_no,P.part_name,S.pack_no, \
                S.pack_date,T.box_no,T.total_box,T.tag_no,T.quantity \
                FROM tags T \
                INNER JOIN packs S ON T.pack_id=S.id \
                INNER JOIN products P ON S.product_id=P.id \
                INNER JOIN customers C ON P.customer_id=C.id \
                INNER JOIN printers PT ON T.printer_id=PT.id \
                WHERE T.wait_print='Y' AND PT.printer_name='"+str(self.tag_printer_name)+"' \
                ORDER BY T.id")
            myresult = mycursor.fetchall()
            i=0
            for t in myresult:
                print(t)
                self.tag_printer.print_tag(t[1],t[2],t[3],t[4],t[5].strftime("%d %b %Y"),str(t[6]),str(t[7]),str(t[8]),str(t[9]))
                mycursor.execute("UPDATE tags SET wait_print='N' WHERE id="+str(t[0]))
                i=i+1
                if i%5==4:
                    time.sleep(0.5)

            mydb.commit()

        QTimer.singleShot(2000, self.print_label_tag)

app = QApplication(sys.argv)
w = MainWindow()
w.show()
app.exec_()

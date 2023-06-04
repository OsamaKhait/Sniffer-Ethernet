#!/usr/bin/python3

import struct
import datetime
from decimal import	Decimal
from Fonc_T import *

#Cette fonction c'est pour convertir binaire en héxadécimal
def convert_binary_hex(DlstO) : 
    hexa = DlstO.hex() 

    return hexa


#Convertion le binaire en double
def convert_to_float_double(Doctets): 
    flotant = struct.unpack('>d', Doctets)[0] 

    return flotant



# Pour obtenir l'adresse IP
def convert_to_ip(Doctets): 
    lst = [] 
    for i in range(0,4,1): #Séparation des octets
        lst.append(Doctets[i:i+1])
    lst_int = [] #Pour stocker chaque nombre dans un élement
    for i in lst: 
        integer = int.from_bytes(i, byteorder="big",signed=False) 
        str_int = str(integer) 
        lst_int.append(str_int)
    ip = ".".join(lst_int)   

    return ip   



 #Pour Concaténer l'adresse MAC
def mac_address(mac):
    str_mac = str(mac) 
    result = ''
    for i in range(0,len(str_mac),2): 
        result += str_mac[i:i+2] + ":"

    return (result.strip(":"))




#Pour afficher la date
def date_affiche(date):  
    date_base = datetime.datetime(1970,1,1,0,0,0) # la date du début
    date_data = datetime.timedelta(seconds=date) # la date à ajouter
    date_result = date_base + date_data #les 2 dates ensembles

    return(date_result.strftime("%A:%d:%b:%m:%Y:%H:%M:%S.%f")) 



#Pour le PacketDate
def PacketDate_affiche(date): 
    date_base = datetime.datetime(2000,1,1,12,0,0) # la date du début
    date_data = datetime.timedelta(seconds=date) # la date à ajouter
    date_result = date_base + date_data # les 2 dates ensembles

    return(date_result.strftime("%A:%d:%b:%m:%Y:%H:%M:%S.%f"))


#Pour convertir le binaire en décimal
def convert_to_dec(binary): 
    int_size = struct.unpack('>i', binary)[0]

    return int_size


#Fonction pour convertir le binaire en décimal pour le fields
def convert_fields(field): 
    int_field = int.from_bytes(field,byteorder='big',signed=False)
    return int_field



# Pour convertir le binaire en héxadécimal pour les fields
def convert_field_hex(field): 
    hex_field = field.hex()
    return hex_field



#Pour convertir un bit en décimal pour le fields
def convert_fields_by_bits(octd,octf,bitd,bitf,binary): 
    n = octf - octd                 
    dec = convert_fields(binary)    
    dec_bin = str(bin(dec))[2:]     
    dec_bin = dec_bin.zfill(n*8)    
    bit = dec_bin[bitd:bitf]        
    nombre = int(bit,2)             

    return nombre


def check_FT(nb,numFT):
    #Si le numéro de la FT est égal à 1 alors on regarde dans le dictionnaire de FT1
    if numFT == 1:
        for cle,val in FT1.items():
            if nb == cle:
                nb = str(cle) + "(" + str(val) + ")"

    if numFT == 2:
        for cle,val in FT2.items():
            if nb == cle:
                nb = str(cle) + "(" + str(val) + ")"
    
    if numFT == 3:
        for cle,val in FT3.items():
            if nb == cle:
                nb = str(cle) + "(" + str(val) + ")"

    if numFT == 4:
        for cle,val in FT4.items():
            if nb == cle:
                nb = str(cle) + "(" + str(val) + ")"

    if numFT == 5:
        for cle,val in FT5.items():
            if nb == cle:
                nb = str(cle) + "(" + str(val) + ")"

    if numFT == 7:
        for cle,val in FT7.items():
            if nb == cle:
                nb = str(cle) + "(" + str(val) + ")"

    if numFT == 0:
        for cle,val in FT0.items():
            if nb == cle:
                nb = str(cle) + "(" + str(val) + ")"

    if numFT == 6:
        for cle,val in FT6.items():
            if nb == cle:
                nb = str(cle) + "(" + str(val) + ")"
    
    if numFT == "IP":
        for cle,val in IP.items():
            if nb == cle:
                nb = str(cle) + "(" + str(val) + ")"
    
    if numFT == "MAC":
        for cle,val in MAC.items():
            if nb == cle:
                nb = str(cle) + "(" + str(val) + ")"

    return nb



def fichier(nfic): #prend le nom du fichier en entrée
    with open(nfic, "rb") as fic: #on ouvre le fichier en binaire
        liste_fic = []
        lines = fic.readlines() #lit chaque ligne 
        liste_fic.append(lines)
        obsw1 = lines[7].decode().rstrip().split(": ")[1]
        obsw2 = lines[8].decode().rstrip().split(": ")[1]
        obsw = obsw1 + " " +obsw2 #concataine les 2 valeurs de obsw
        liste_fic.append(obsw)
        bds = lines[9].decode().rstrip().split(": ")[1]
        liste_fic.append(bds)
        tv = lines[10].decode().rstrip().split(": ")[1]
        liste_fic.append(tv)
        dt = lines[14].decode().rstrip().replace('"', '').split(": ")[1] #enleve les guillemets pour gérer les pb en csv
        liste_fic.append(dt)
        nom = lines[27].decode().rstrip().split(": ")[1]
        liste_fic.append(nom)
    
    return liste_fic
#!/usr/bin/python3

import argparse, sys
import mysql.connector
from fonctions import *
from field import *
import time

start_time = time.time()

if __name__ == '__main__': 

    #Les arguemnts à utiliser pour l'éxecution du programme
    argParser = argparse.ArgumentParser()
    # Argument -b pour le fichier binaire
    argParser.add_argument("-b", "--brut", type=str, help="Fichier Binaire") 
    # Argument -r pour le fichier rep
    argParser.add_argument("-r", "--rep", type=str, help="Fichier rep") 
    # Si l'utilisateur ne tape aucun argument, le script renvoie l'aide de l'option -h
    args = argParser.parse_args(args=None if sys.argv[1:] else ['--help']) 
    
    # Place les valeurs des arguments dans la variable args
    args= argParser.parse_args() 


    # Connexion à la base de données
    connection = mysql.connector.connect(host='localhost', 
                                        database='thales',
                                        user='root',
                                        password='')
    
    # Fonction récupére les informations du fichier rep (definit dans le fichier fonctions.py)       
    rep = fichier(args.rep) 


    # Les informations du fichier rep 
    obsw = rep[1] 
    bds = rep[2]
    tv = rep[3]
    dt = rep[4]
    nt = rep[5]

    # Création d'un curseur afin d'initier des requêtes SQL auprès de la base
    curseur = connection.cursor() 
    
    # Création de la requête pour récupèrer les dates d'exécution de tous les fichiers déjà insérés dans la base, et l'executer 
    mySql_select_query = """SELECT dt FROM fic""" 
    curseur.execute(mySql_select_query) 
    
    # Place dans une variable toutes les informations récupérer depuis la base
    res = curseur.fetchall() 
    
    # Pour chaque date de fichier dans la base, la compare à la date du fichier rep afin de ne pas l'envoyé si elles coïncident
    for i in res: 
        i =  "('%s',)" % i
        if i == f"('{dt}',)":
            sys.exit("Les données de ce fichier sont déjà été insérées dans la base\nErreur : Date de fichier rep correspond à une date de fichier dans la base")

    # Création de la requête pour envoyer les informations du fichier rep dans la base        
    mySql_insert_query = """INSERT INTO fic (nomfic, obsw, bds, tv, dt)
                                VALUES (%s, %s, %s, %s, %s) """ 

    # Création d'une variable contenant les informations à envoyer, et l'executer                            
    record = (nt, obsw, bds, tv, dt,) 
    curseur.execute(mySql_insert_query, record)

    # Récupèration du dernier id incrémenté par l'envoi du fichier rep dans la base
    numfic = curseur.lastrowid 

    #Ouvreture du fichier binaire
    with open(args.brut, 'rb') as binary: 
        trame = 0 
        fic = binary.read() #fic = Le contenue du fichier binaire
        state = True 
        cpt = 0 


        while state == True: 
            
            field1 = convert_field_hex(fic[trame+40:trame+42]) #Lecture du premier field pour déterminer le type de trame
            liste_field = [] 

            if field1 == '0800': 
                date = date_affiche(convert_to_float_double(fic[trame+8:trame+16])) #Lecture de la date
                mac = check_FT(mac_address(convert_binary_hex(fic[trame+28:trame+34])),"MAC") #Première addresse MAC
                mac2 = check_FT(mac_address(convert_binary_hex(fic[trame+34:trame+40])),"MAC") #Deuxième adresse MAC
                ip = check_FT(convert_to_ip(fic[trame+54:trame+58]),"IP") #Première addresse IP
                ip2 = check_FT(convert_to_ip(fic[trame+58:trame+62]),"IP") #Deuxième addresse IP
                b3 = convert_fields(fic[trame+16:trame+20]) #bench3
                b5 = check_FT(convert_fields_by_bits(19,23,12,16,fic[trame+19:trame+23]),0) #bench5

                liste_field.append(field1) #field1
                #Décodage en décimal a partir de field2
                liste_field.append(convert_fields(fic[trame+42:trame+44])) #field2 
                liste_field.append(convert_fields(fic[trame+44:trame+46])) #field3 
                liste_field.append(convert_fields(fic[trame+46:trame+48])) #field4 
                liste_field.append(convert_fields(fic[trame+48:trame+50])) #field5 
                liste_field.append(convert_fields(fic[trame+50:trame+51])) #field6 
                liste_field.append(convert_fields(fic[trame+51:trame+52])) #field7 
                liste_field.append(convert_fields(fic[trame+62:trame+64])) #field9 
                liste_field.append(convert_fields(fic[trame+64:trame+66])) #field10 
                liste_field.append(convert_fields(fic[trame+66:trame+68])) #field11 
                liste_field.append(check_FT(convert_fields_by_bits(70,72,3,4,fic[trame+70:trame+72]),7)) #field14 puis on verifie Fonction de Transfer
                liste_field.append(convert_fields_by_bits(70,72,5,8,fic[trame+70:trame+72])) #field16 
                liste_field.append(check_FT(convert_fields_by_bits(70,72,8,11,fic[trame+70:trame+72]),5)) #field17  puis on verifie Fonction de Transfer
                liste_field.append(check_FT(convert_fields_by_bits(70,72,11,16,fic[trame+70:trame+72]),2)) #field18  puis on verifie ça Fonction de Transfer
                liste_field.append(convert_fields_by_bits(72,74,2,16,fic[trame+72:trame+74])) #field20 
                liste_field.append(convert_fields(fic[trame+74:trame+76]))#field21 
                liste_field.append(convert_fields_by_bits(76,77,4,5,fic[trame+76:trame+77])) #field23 
                liste_field.append(convert_fields_by_bits(76,77,6,7,fic[trame+76:trame+77])) #field25 
                liste_field.append(convert_fields_by_bits(76,77,7,8,fic[trame+76:trame+77])) #field26 
                liste_field.append(check_FT(convert_fields_by_bits(77,78,2,8,fic[trame+77:trame+78]),3)) #field28 puis on verifie ca Fonction de Transfer
                liste_field.append(check_FT(convert_fields_by_bits(78,80,0,6,fic[trame+78:trame+80]),4)) #field29 puis on verifie ça Fonction de Transfer
                liste_field.append(convert_fields_by_bits(78,80,6,16,fic[trame+78:trame+80])) #field30 
                liste_field.append(check_FT(convert_fields(fic[trame+81:trame+82]),1)) #field32  puis on verifie ça Fonction de Transfer
                liste_field.append(convert_fields(fic[trame+82:trame+86])+((convert_fields(fic[trame+86:trame+88]))/2**16)) #field33/34 +35 
                TimePacket = PacketDate_affiche(convert_fields(fic[trame+82:trame+86])+((convert_fields(fic[trame+86:trame+88]))/2**16)) #TimePacket
                
                #Création d'un objet appelé objectfield pour stocker tous les fields
                objectfield = Field(liste_field) 
                #On récuperer les fields qui nous intéresses puis ensuite les mettre en binaire pour les concaténer
                field14 = str(bin(convert_fields_by_bits(70,72,3,4,fic[trame+70:trame+72])))[2:]
                field18 = str(bin(convert_fields_by_bits(70,72,11,16,fic[trame+70:trame+72])))[2:]
                #fill avec des 0 pour obtenir la taille voulue
                field18 = field18.zfill(5) 
                field28 = str(bin(convert_fields_by_bits(77,78,2,8,fic[trame+77:trame+78])))[2:]
                field28 = field28.zfill(6) 
                field29 = str(bin(convert_fields_by_bits(78,80,0,6,fic[trame+78:trame+80])))[2:]
                field29 = field29.zfill(6) 
                field30 = str(bin(objectfield.lst_field[21]))[2:] #5 101 10
                field30 = field30.zfill(10) 

                FT_6 = field14 + field18 + field28 + field29 + field30 

                #transforme en entier pour pouvoir le mettre en hexadécimal
                FT_6 = int(FT_6,2) 
                FT_6 = hex(FT_6) 
                FT_6 = check_FT(FT_6,6) 
                objectfield.lst_field.append(FT_6) 
                
            elif field1 == '0806':
                date = date_affiche(convert_to_float_double(fic[trame+8:trame+16])) #FrameDate
                b3 = convert_to_dec(fic[trame+16:trame+20]) 
                b5 = check_FT(convert_fields_by_bits(19,23,12,16,fic[trame+19:trame+23]),0) 
                liste_field.append(field1) #field1
                mac = check_FT(mac_address(convert_binary_hex(fic[trame+28:trame+34])),"MAC") #addresse MAC Dest
                mac2 = check_FT(mac_address(convert_binary_hex(fic[trame+34:trame+40])),"MAC") #addresse MAC Source
                liste_field.append(convert_fields(fic[trame+42:trame+44])) #field2
                liste_field.append(convert_fields(fic[trame+44:trame+46])) #field3
                liste_field.append(convert_fields(fic[trame+46:trame+47])) #field4
                liste_field.append(convert_fields(fic[trame+47:trame+48])) #field5
                liste_field.append(convert_fields(fic[trame+48:trame+50])) #field6
                mac_send = check_FT(mac_address(convert_binary_hex(fic[trame+50:trame+56])),"MAC") 
                ip_send = check_FT(convert_to_ip(fic[trame+56:trame+60]),"IP") 
                mac_target = check_FT(mac_address(convert_binary_hex(fic[trame+60:trame+66])),"MAC") 
                ip_target = check_FT(convert_to_ip(fic[trame+66:trame+70]),"IP")

                # Création d'un objet avec le constructeur Field pour ranger toutes les informations des trames
                objectfield = Field(liste_field) 

            #En cas si il n'y a pas d'erreur
            try: 
                taille = fic[trame+24:trame+28]
                taille = convert_to_dec(taille)
                trame = trame + taille + 28
            except struct.error: #En cas si il y a une erreur on marque fin du fichier et on passe state à false
                print("Fin du fichier")
                state = False

            if field1 == '0800':

                mySql_insert_query = """INSERT INTO trame800 (numfic, date, pmid, bench3, bench5, framesize, macdst, macsrc, field1, field2, field3, field4, field5, field6, field7, ipsrc,
                    ipdst, field9, field10, field11, field14, field16, field17, field18, field20, field21, field23, field25, field26, field28, field29, field30, field32, field333435,
                    timepacket) 
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s) """
                record = (numfic, date, FT_6, b3, b5, taille, mac, mac2, field1, objectfield.lst_field[1], objectfield.lst_field[2],objectfield.lst_field[3],objectfield.lst_field[4],
                    objectfield.lst_field[5],objectfield.lst_field[6],ip, ip2, objectfield.lst_field[7],objectfield.lst_field[8],objectfield.lst_field[9],objectfield.lst_field[10],
                    objectfield.lst_field[11],objectfield.lst_field[12],objectfield.lst_field[13],objectfield.lst_field[14],objectfield.lst_field[15],objectfield.lst_field[16],
                    objectfield.lst_field[17],objectfield.lst_field[18],objectfield.lst_field[19],objectfield.lst_field[20],objectfield.lst_field[21],objectfield.lst_field[22],objectfield.lst_field[23],TimePacket,)
                curseur.execute(mySql_insert_query, record)
            
            elif field1 == '0806': #si le field est égal à 0806 alors

                mySql_insert_query = """INSERT INTO trame806 (numfic, date, bench3, bench5, framesize, macdst, macsrc, field1, field2, field3, field4, field5, field6,
                    macsender, ipsender, mactarget, iptarget) 
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s) """
                record = (numfic, date, b3, b5, taille, mac, mac2, field1, objectfield.lst_field[1], objectfield.lst_field[2], objectfield.lst_field[3], objectfield.lst_field[4], objectfield.lst_field[5],
                    mac_send, ip_send, mac_target, ip_target,)
                curseur.execute(mySql_insert_query, record)

            cpt += 1 #Incrémentation du compteur

        print(cpt)#Le nombre des lignes lues

        connection.commit()

        end_time = time.time()
        execution_time = end_time - start_time
        print("Temps d'exécution : ", execution_time, "secondes")
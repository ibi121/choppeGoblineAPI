<?php

//Liens vers la BD sur heroku?
//https://api-php-android.herokuapp.com/

/**
 * s'occupe de la connection, retourne un objet PDO qui est en sois la conenction de la BD.
 * devrait surement s'appeler BD?
 *
 * @return PDO
 */
function connection()
{
    $hostname = "mysql-d226753-mec-38a8.aivencloud.com";
    $port = "11402";
    $username = "avnadmin";
    $password = "AVNS_mvvDOLqEa9h3Ei9O1_w";

    try {
        $conn = new PDO("mysql:host=$hostname;port=$port;dbname=choppeGobline", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    return $conn;
}


function InsererDansLaBd($courriel, $motDePasse, $addresse, $nom, $telephone, $points)
{
    try {

        $sql = "INSERT INTO client(nom, courriel, motDePasse, telephone, addresse, points)
VALUES('$nom','$courriel', '$motDePasse', '$addresse', '$telephone', '$points')";
        if (connection()->query($sql) === TRUE) {
            echo "nouvel utilisateur a bel et bien ete inserer";
        }
    } catch (PDOException $exception) {
        die("could not connect to db" . $exception->getMessage());
    }
}

function login($courriel, $motDePasse)
{
    try {
        $sql = "SELECT * FROM client WHERE courriel = '$courriel' AND motDePasse = '$motDePasse'";

        $users = connection()->query($sql);
        $users->setFetchMode(PDO::FETCH_ASSOC);
        return $users->fetch();
    } catch (PDOException $e) {
        die("could not connect to db" . $e->getMessage());
    }
}

function CreerCommande($nomClient, $montantCommande, $dateCommande, $clientId, $itemId)
{
    try {
        $sql = "INSERT INTO commande(nomClient, montantCommande, dateCommande, clientId, itemId) 
VALUES('$nomClient', '$montantCommande', '$dateCommande', '$clientId', '$itemId')";
        if(connection()->query($sql) == TRUE){
            echo "a new order has been created";
        }
    }catch (PDOException $exception){
        die("could not connect to db" . $exception->getMessage());
    }
}



function CreerItem($sorte, $taille, $prix)
{
    try {
        $sql = "INSERT INTO items(sorte, taille, prix) 
VALUES('$sorte', '$taille', '$prix')";
        if(connection()->query($sql) == TRUE){
            echo 'succes son';
        }
    }catch (PDOException $exception){
        die("could not connect to db" . $exception->getMessage());
    }
}



function UpdateClient($connectedUser, $pointsARentrer){
    try{
        $sql = "UPDATE client SET points = '$pointsARentrer' WHERE id = '$connectedUser' ";
        connection()->query($sql);
    } catch (PDOException $exception) {
        die('could not update table');
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if ($_POST['action'] == 'insertUser') {
        /**
         * recois les requetes  post pour les inserer en BD
         */
        $courrielPost = $_POST["courriel"];
        $motDePassePost = $_POST["motDePasse"];
        $numeroDeTelephonePost = $_POST["telephone"];
        $nomPost = $_POST["nom"];
        $addressePost = $_POST['addresse'];
        $pointsPost = $_POST['points'];

        if (isset($courrielPost, $motDePassePost, $numeroDeTelephonePost, $nomPost, $addressePost, $pointsPost)) {
            InsererDansLaBd($courrielPost, $motDePassePost, $numeroDeTelephonePost, $nomPost, $addressePost, $pointsPost);
            echo "a new user has been set into the database. thank you";
        }
        /**
         * Permet d'update les points de l'utilisateur
         * A besoin du id de l'utilisateur connecte ainsi que le nombre de points a rentrer
         */
    } else if ($_POST['action'] == 'updateClient') {
        $connectedUser = $_POST['connectedUser'];
        $pointsARentrer = $_POST['points'];

        if (isset($connectedUser, $pointsARentrer)) {
            UpdateClient($connectedUser, $pointsARentrer);
            echo 'User has been updated succesfully';
        } else {
            echo 'user has not been updated';
        }

    } else if($_POST['action'] == 'insertItem'){
        $sorte = $_POST['sorte'];
        $taille = $_POST['taille'];
        $prix = $_POST['prix'];

        if(isset($sorte, $taille, $prix)){
            CreerItem($sorte, $taille, $prix);
            echo"a new item has been created";
        }else {
            echo "an error has occured with creating an item";
        }
    }else if($_POST['action'] == "insertCommande"){
        $nomClient = $_POST['nom'];
        $montantCommande = $_POST['montantCommande'];
        $dateCommande = $_POST['dateCommande'];
        $clientId = $_POST['idClient'];
        $itemId = $_POST['idItem'];

        if(isset($nomClient, $montantCommande, $dateCommande, $clientId, $itemId)){
            CreerCommande($nomClient, $montantCommande, $dateCommande, $clientId, $itemId);
        }else {
            echo "an error has occured in the creation of the order";
        }
    }
}

/**
 * Toutes les methodes get, séparer par un nom d'action (header)
 */
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    /**
     * fetch les data du get :o)
     */
    if ($_GET['action'] == 'loginUser') {
        $courrielGet = $_GET["courriel"];
        $motDePasseGet = $_GET["motDePasse"];

        if (isset($courrielGet, $motDePasseGet)) {
            $users = login($courrielGet, $motDePasseGet);
            echo json_encode($users);
            exit();
        } else {
            echo "og no sowwry :o(";
        }
    }


}












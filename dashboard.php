<?php

session_start();

if($_SESSION["logged_in"] != true) {
    header('Location: index.php');
}

// Connexion à la base de donnée MYSQL
$pdo = new PDO('mysql:host=localhost;dbname=depenses', 'root', 'root');
$pdo->exec("SET  NAMES UTF8");

// Données utilisateur
$identifiant = $_SESSION["identifiant"];
$query = "SELECT * from utilisateurs where nom = :identifiant";
$stmt = $pdo->prepare($query);
$stmt->execute(['identifiant' => $identifiant]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

$id = $data["id"];




// Ajouter une dépense
if (isset($_POST['addDepense'])) {

    $sommedepense = $_POST["sommedepense"];
    $nomdepense = $_POST["namedepense"];
    $sql = "INSERT INTO  depenses(`id_utilisateur`, `somme`, `nom`) VALUES (?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id,$sommedepense, $nomdepense]);




// Retirer une dépense 
}
$depensetotal = 0;
if(isset($_POST['delete']) && isset($_POST['depense'])) {
    $depenseselect = $_POST['depense'];
    $selectgainsomme = $_POST["depenseselectsomme"];
    $query = "DELETE FROM depenses WHERE id = :did";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['did' => $depenseselect]);


    
}

// Récupérer la liste des dépenses 
$query = "SELECT * from depenses where id_utilisateur = :identifiant";
$stmt = $pdo->prepare($query);
$stmt->execute(['identifiant' => $id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ajouter un gain 
if (isset($_POST['addGain'])) {


    $sommegain = $_POST["sommegain"];
    $nomgain = $_POST["namegain"];
    $sql = "INSERT INTO gains(`id_utilisateur`, `somme`, `nom`) VALUES (?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id,$sommegain, $nomgain]);

}
// Total gain :
$tmp = 0;
// Retirer un gain 
if(isset($_POST['deletegain']) && isset($_POST['gainselect'])) {
    $gainselect = $_POST['gainselect'];
    $sommeselect = $_POST["gainselectsomme"];
    $query = "DELETE FROM gains WHERE id = :did";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['did' => $gainselect]);
    
    
}


?>

<html>
    <head>
        <meta charset="utf-8">
        <title>Dashboard</title>
        <link rel="stylesheet" href="style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    </head>
    <body>

        <br>
        <h1>Bienvenue <?= $_SESSION["identifiant"]?></h1>
        <br>




<br><br><br>

<!-- Depenses ajouter  -->
<div class="depenses">
<h3>Ajouter une dépense</h3>
<br><br><br>
<form action="" method="post">
<input type="number" name="sommedepense" id="sommedepense">
<label for="sommedepense">Somme dépense</label>
<br>
<br>
<input type="text" name="namedepense" id="namedepense">
<label for="namedepense">Nom de la dépense</label>
<br>
<br>
<input type="submit" name="addDepense" class="btn btn-primary" value="Ajouter">
</form>
</div>
<!-- Liste des dépenses  -->
<h4>Liste des dépenses :</h4>
<table>
    <?php foreach($data as $row): ?>
    <tr>
        <td><?= $row["somme"]; ?>€</td>
        <?php
                $depensetotal += $row["somme"];

        ?>
        <td><?= $row["nom"]; ?></td>
        <td>
            <form action="" method="post">
                <input type="hidden" name="depense" value="<?= $row["id"]; ?>">
                <input type="hidden" name="depenseselectsomme" value="<?= $row["somme"]; ?>">

                <button type="submit" name="delete" class="btn btn-danger">Supprimer</button>

            </form>
        </td>


    </tr>
    <?php endforeach; ?>


</table>

<!-- Ajouter un gain  -->
<br>
<br>
<div class="gains">
<h3>Ajouter un gain</h3>
<br>
<br>
<br>
<form action="" method="post">
<input type="number" name="sommegain" id="sommegain">
<label for="sommegain">Somme gain</label>
<br>
<br>
<input type="text" name="namegain" id="namegain">
<label for="namegain">Nom du gain</label>
<br>
<br>
<input type="submit" name="addGain" class="btn btn-primary" value="Ajouter">
</form>
</div>
<!-- Liste des gains  -->
<h4>Liste des gains :</h4>


<?php 
$query = "SELECT * from gains where id_utilisateur = :identifiant";
$stmt = $pdo->prepare($query);
$stmt->execute(['identifiant' => $id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($data as $row): ?>
    <tr>
        <td><?= $row["somme"]; ?>€</td>
        <?php
        $tmp += $row["somme"];
        
        
        ?>
        <td><?= $row["nom"]; ?></td>
        <td>
            <form action="" method="post">
                <input type="hidden" name="gainselect" value="<?= $row["id"]; ?>">
                <input type="hidden" name="gainselectsomme" value="<?= $row["somme"]; ?>">

                <button type="submit" name="deletegain" class="btn btn-danger">Supprimer</button>
            </form>
        </td>


    </tr>
    <?php endforeach; ?>
</table>

<br><br><br>
<!-- Total complet des dépenses (Infontionnel) -->
<div class="total-gains">
<h2>Total des gains :</h2><br><br>
<h3><?= $tmp; ?>
<?= "€" ?></h3>
<br>
</div>

<br><br><br>
<div class="total-depenses">
<h2>Total des dépenses :</h2><br><br>

<h3><?= $depensetotal ?>€</h3>
<br>
</div>

<br><br><br>
<?php 
$tmp -= $depensetotal;

?>
<br>
<button class="monthly-total">Total du compte de ce mois-ci <?= $tmp?>€</button>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>


</body>
</html>
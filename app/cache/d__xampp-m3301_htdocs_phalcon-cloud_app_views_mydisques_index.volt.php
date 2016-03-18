<?php if ($userCo) { ?>
<h1>Mes disques -> <?php echo $user->login; ?> (<?php echo $user->prenom; ?> <?php echo $user->nom; ?>)</h1>
    <?php echo $liste; ?>
<?php } else { ?>
    Vous devez vous connecter
<?php } ?>



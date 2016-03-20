<?php if ($userCo) { ?>
<h1>Mes disques -> <?php echo $user->login; ?> (<?php echo $user->prenom; ?> <?php echo $user->nom; ?>)</h1>
    <?php echo $q['btCrea']; ?>
    <?php echo $liste; ?>
<?php } else { ?>
    Vous devez vous connecter pour accéder a cette page
<?php } ?>

<?php echo $script_foot; ?>
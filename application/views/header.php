<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper(['url','html']); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Series</title>
    <?php echo link_tag('public/css/spectre.min.css'); ?>
    <?php echo link_tag('public/css/spectre-icons.min.css'); ?>
    <?php echo link_tag('public/css/spectre-exp.min.css'); ?>
    <?php echo link_tag('public/css/custom.css'); ?>

</head>
<body>
  <header class="navbar bg-primary">
  <section class="navbar-section">
    <a href="<?php echo site_url(); ?>"
       class="navbar-brand mr-2 px-2 text-secondary">
           <img class="icon icon-2x" src="<?=base_url('icon.png')?>">
           Mes Séries
    </a>
<!--    <a href="#" class="btn btn-primary">Calendrier</a>-->
    <a href="<?= site_url('search/'); ?>" class="btn btn-primary">Rechercher</a>
    <a href="<?= site_url('category/'); ?>" class="btn btn-primary">Catégories</a>

    <?php if (isset($email)):?>
      <a href="<?= site_url('home');?>" class="btn btn-primary">Perso</a>
    <?php endif;?>
  </section>

<?php if (isset($email)): ?>
  <section class="navbar-section">
    <form action="<?php echo site_url("welcome/logout"); ?>" method="post">
    <div class="input-group input-inline">
      <span class="input-group-addon text-primary">
        <i class="form-icon icon icon-people text-primary"></i>
        <?php echo $email." "; ?></span>
      <button type="submit" class="btn btn-secondary input-group-btn">Déconnexion</button>
    </div>
  </form>
  </section>
<?php else: ?>
  <section class="navbar-section">
    <form action="<?php echo site_url("welcome/login"); ?>" method="post">
    <div class="input-group input-inline has-icon-left">
        <input class="form-input" name="email" type="email" placeholder="email">
        <i class="form-icon icon icon-people text-primary"></i>
      <input class="form-input" name="password" type="password" placeholder="password">
      <button type="submit" class="btn btn-secondary input-group-btn">Se connecter/S'inscrire</button>
    </div>
  </form>
  </section>
<?php endif; ?>
</header>

<?php
// Isolated component render: no application bootstrap or database connection.
function route($name, ...$parameters) { return '#dashboard'; }
function url($path = '') { return $path; }
require dirname(__DIR__, 2).'/vendor/autoload.php';
$root = dirname(__DIR__, 2);
$compiler = new Illuminate\View\Compilers\BladeCompiler(new Illuminate\Filesystem\Filesystem(), __DIR__);
$compiled = $compiler->compileString(file_get_contents($root.'/resources/views/partials/logo.blade.php'));
file_put_contents(__DIR__.'/compiled-logo.php', $compiled);
ob_start();
eval('?>'.$compiled);
$brand = ob_get_clean();
$nav = '<a href="#dashboard">Dashboard</a><a href="#offres">Offres</a><a href="#profil">Mon profil</a>';
$head = '<!doctype html><html lang="fr"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>SmartRecruit — aperçu du logo</title><link rel="icon" type="image/svg+xml" href="/assets/brand/smartrecruit-mark.svg"><link rel="stylesheet" href="/assets/app.css"><body>';
$topbar = '<header class="topbar">'.$brand.'<nav class="nav">'.$nav.'</nav><div class="session"><span class="role">student</span></div></header>';
$main = '<main class="page" id="dashboard"><div class="page-head"><div><h1>Votre prochaine rencontre professionnelle.</h1><p>Étudiants et entreprises, connectés par SmartRecruit.</p></div></div><div class="card"><h2>Aperçu de l’identité</h2><p>Logo dans la navigation de l’application.</p></div></main>';
file_put_contents(__DIR__.'/index.html', $head.$topbar.$main.'</body></html>');
$sidebar = '<div class="app-shell"><aside class="sidebar">'.$brand.'<nav class="sidebar-nav">'.$nav.'<a href="#console">Console</a><a href="#comptes">Comptes</a><a href="#candidatures">Candidatures</a></nav><div class="sidebar-foot"><span class="role">admin</span></div></aside>'.$main.'</div>';
file_put_contents(__DIR__.'/admin.html', $head.$sidebar.'</body></html>');
echo "Logo Blade rendered; isolated desktop and sidebar previews generated.\n";

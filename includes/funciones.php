<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

function esUltimo(string $actual, string $proximo): bool{
    if($actual !== $proximo) {
        return true;
    }
    return false;
}

function esAdmin(): void {
    if(!isset($_SESSION['admin'])){
       header('Location: /') ;
    }
}

// FUnción que revisa que el usuario esté autenticado

function isAuth(): void {
    if(!isset($_SESSION['login'])) {
        header('Location: /');
    }
}
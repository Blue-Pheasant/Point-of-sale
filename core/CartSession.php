<?php

namespace app\core;

class CartSession
{
    const SessionKey = 'SHOPPINGCART';

    public static function Store($Cart) {
        if (!isset($_SESSION)) { session_start(); }
        $_SESSION[CartSession::SessionKey] = $Cart;
    }

    public static function Exists() {
        if (!isset($_SESSION)) { session_start(); }
        return isset($_SESSION[CartSession::SessionKey]);
    }

    public static function Get() {
        if (CartSession::Exists()) {
            if (!isset($_SESSION)) { session_start(); }
            return $_SESSION[CartSession::SessionKey];
        } else {
            return null;
        }
    }

    public static function Remove() {
        if (CartSession::Exists()) {
            if (!isset($_SESSION)) { session_start(); }
            if (isset($_SESSION[CartSession::SessionKey])) {
            unset($_SESSION[CartSession::SessionKey]);
            }
        }
    }
}
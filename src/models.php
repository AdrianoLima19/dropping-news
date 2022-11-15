<?php

declare(strict_types=1);

namespace App;

enum Http: int
{
  case CONTINUE = 100;
  case OK = 200;
  case CREATED = 201;
  case ACCEPTED = 202;
  case NO_CONTENT = 204;
  case MOVED_PERMANENTLY = 301;
  case PERMANENT_REDIRECT = 308;
  case BAD_REQUEST = 400;
  case UNAUTHORIZED = 401;
  case FORBIDDEN = 403;
  case NOT_FOUND = 404;
  case METHOD_NOT_ALLOWED = 405;
  case TOO_MANY_REQUESTS = 429;
  case INTERNAL_SERVER_ERROR = 500;
  case NOT_IMPLEMENTED = 501;
  case BAD_GATEWAY = 502;
  case SERVICE_UNAVAILABLE = 503;
}

enum User: int
{
  case ADMIN = 82;
  case USER = 35;
  case GUEST = 27;
}

class Env
{
  public static function parseEnv()
  {
    if (file_exists(root() . ".env"))       $file = root() . ".env";
    if (file_exists(root() . ".env.local")) $file = root() . ".env.local";

    if (empty($file)) return;

    $file = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($file as $line) {
      if (strpos(trim($line), '#') === 0) continue;

      list($name, $value) = explode('=', $line, 2);

      $name = trim($name);
      $value = trim($value);

      $_ENV[$name] = $value;
    }
  }
}

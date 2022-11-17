<?php

declare(strict_types=1);

if (!function_exists('DBO')) {
  /**
   * @return \PDO|null
   */
  function DBO(): ?\PDO
  {
    static $instance;

    if (is_null($instance)) {
      $driver =   env('DB_CONNECTION', 'mysql');
      $host =     env('DB_HOST',       '127.0.0.1');
      $port =     env('DB_PORT',       '3306');
      $database = env('DB_DATABASE',   'dropping-news');
      $username = env('DB_USERNAME',   'root');
      $password = env('DB_PASSWORD',   '');

      try {
        $instance = new \PDO(
          "{$driver}:host={$host};port={$port};dbname={$database}",
          $username,
          $password,
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_CASE => PDO::CASE_NATURAL
          ]
        );
      } catch (\Throwable $th) {
        throw $th;
      }
    }

    return $instance;
  }
}

if (!function_exists('root')) {
  /**
   * @return string
   */
  function root(): string
  {
    return dirname(__DIR__) . "/";
  }
}

if (!function_exists('path')) {
  /**
   * @return string
   */
  function path(): string
  {
    return strtok(str_replace(dirname($_SERVER['PHP_SELF']), "", $_SERVER['REQUEST_URI']) ?? '', '?');
  }
}

if (!function_exists('url')) {
  /**
   * @return string
   */
  function url(): string
  {
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $url = strtok($url, '?');

    if (path() === '/') return trim($url, "/") ?? '';
    return trim(str_replace(path(), "", $url), "/") ?? '';
  }
}

if (!function_exists('route')) {
  /**
   * @param  string $path
   *
   * @return string
   */
  function route(string $path): string
  {
    return url() . "/" . trim($path, "/");
  }
}

if (!function_exists('redirect')) {
  /**
   * @param  string $path
   *
   * @return void
   */
  function redirect(string $path)
  {
    return header("location: " . route($path));
  }
}

if (!function_exists('env')) {
  /**
   * @param  string $key
   * @param  mixed  $value
   *
   * @return mixed
   */
  function env(string $key, mixed $value = null): mixed
  {
    return $_ENV[$key] ?? $value;
  }
}

if (!function_exists('assets')) {
  /**
   * @return string
   */
  function assets(string $file): string
  {
    return url() . "/resources/" . trim($file, '/');
  }
}

if (!function_exists('view')) {

  function view(string $template = null, string|array $view, array $params = [])
  {
    if (!empty($template)) {
      $template = trim($template, '/');
      if (!str_contains($template, ".php")) $template .= ".php";
      $template = root() . "views/{$template}";

      if (!file_exists($template)) throw new \RuntimeException("Error Processing Request", 1);
    }

    if (is_string($view)) $view = [$view];

    foreach ($view as $file) {
      $file = trim($file, '/');
      if (!str_contains($file, ".php")) {
        $file .= ".php";
      }
      $file = root() . "views/{$file}";
      if (!file_exists($file)) throw new \RuntimeException("Error Processing Request", 1);
      $views[] = $file;
    }

    $content = '';
    extract($params);

    foreach ($views as $view) {
      ob_start();
      require_once $view;
      $content .= ob_get_contents();
      ob_end_clean();
    }

    if (empty($template)) {
      echo $content;
      return;
    }

    require_once $template;
  }
}

if (!function_exists('warning')) {
  /**
   * @param  string|Stringable $message
   * @param  array             $context
   *
   * @return void
   */
  function warning(string|Stringable $message, array $context = [])
  {
    \App\Log::pushLog('warning', $message, $context);
  }
}

if (!function_exists('error')) {
  /**
   * @param  string|Stringable $message
   * @param  array             $context
   *
   * @return void
   */
  function error(string|Stringable $message, array $context = [])
  {
    \App\Log::pushLog('error', $message, $context);
  }
}

if (!function_exists('critical')) {
  /**
   * @param  string|Stringable $message
   * @param  array             $context
   *
   * @return void
   */
  function critical(string|Stringable $message, array $context = [])
  {
    \App\Log::pushLog('critical', $message, $context);
  }
}

if (!function_exists('alert')) {
  /**
   * @param  string|Stringable $message
   * @param  array             $context
   *
   * @return void
   */
  function alert(string|Stringable $message, array $context = [])
  {
    \App\Log::pushLog('alert', $message, $context);
  }
}

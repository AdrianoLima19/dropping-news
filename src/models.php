<?php

declare(strict_types=1);

namespace App;

use DateTimeZone;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Stringable;

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

class Paginator
{
  private string $url;
  private int $quantity;
  private int $pages;
  private int $page;
  private int $limit;
  private int $range;

  public function __construct(string $url, int $quantity, int $page, int $limit = 10, int $range = 3)
  {
    $this->url = $url;
    $this->quantity = $quantity >= 1 ? $quantity : 1;
    $this->page = $page >= 1 ? $page : 1;
    $this->limit = $limit >= 1 ? $limit : 10;
    $this->range = $range >= 1 ? $range : 3;

    $this->pages = (int)ceil($this->quantity / $this->limit);
    $this->page = $this->page <= $this->pages ? $this->page : $this->pages;
  }

  /**
   * Get the value of pages
   */
  public function getPages()
  {
    return $this->pages;
  }

  /**
   * @return string|null
   */
  public function render(): ?string
  {
    if ($this->limit > $this->quantity) return null;

    if (strpos($this->url, '?')) $this->url .= '&page=';
    else $this->url .= '?page=';

    $beforeOverflow = $afterOverflow = $firstElementNumber = 0;
    $element = '';
    $url = $this->url;

    for ($i = $this->range; $i >= 1; $i--) {
      $index = $this->page - $i;

      if ($index < 1) {
        $beforeOverflow++;
        continue;
      }

      if (empty($firstElementNumber)) $firstElementNumber = $index;

      $element .= '<li class="page-item"> <a class="page-link" href="' . "{$url}{$index}" . '">' . $index . '</a> </li>';
    }

    $element .= '<li class="page-item active" aria-current="page"> <a class="page-link" href="' . "{$url}{$this->page}" . '" >' . $this->page . '</a> </li>';

    for ($j = 1; $j <= $this->range + $beforeOverflow; $j++) {
      $index = $this->page + $j;

      if ($index > $this->pages) {
        $afterOverflow++;
        continue;
      }

      $element .= '<li class="page-item"> <a class="page-link" href="' . "{$url}{$index}" . '">' . $index . '</a> </li>';
    }

    if ($beforeOverflow == 0 && $afterOverflow != 0) {
      for ($k = 1; $k <= $afterOverflow; $k++) {
        if ($firstElementNumber - $k < 1) break;

        $index = $firstElementNumber - $k;
        $link = '<li class="page-item"> <a class="page-link" href="' . "{$url}{$index}" . '">' . $index . '</a> </li>';
        $element = $link . $element;
      }
    }

    $leftArrow = '<li class="page-item"> <a class="page-link ' . ($this->page == 1 ? 'disabled' : '') . '" href="' . "{$url}1" . '"> <i class="fa-solid fa-chevron-left chevrons ms-2"></i> </a> </li>';

    $leftArrow .= '<li class="page-item"> <a class="page-link ' . ($this->page - 1 < 1 ? 'disabled' : '') . '" href="' . $url . ($this->page - 1 < 1 ? 1 : $this->page - 1) . '"> <i class="fa-solid fa-chevron-left"></i> </a> </li>';

    $element = $leftArrow . $element;

    $rightArrow = '<li class="page-item"> <a class="page-link ' . ($this->page + 1 > $this->pages ? 'disabled' : '') . '" href="' . $url . ($this->page + 1 > $this->pages ? $this->pages : $this->page + 1) . '"> <i class="fa-solid fa-chevron-right"></i> </a> </li>';

    $rightArrow .= '<li class="page-item"> <a class="page-link ' . ($this->page == $this->pages ? 'disabled' : '') . '" href="' . $url . $this->pages . '"> <i class="fa-solid fa-chevron-right chevrons me-2"></i> </a> </li>';

    $element = $element . $rightArrow;

    return '<ul class="pagination d-flex justify-content-center align-items-center">' . $element . '</ul>';
  }

  /**
   * @return array
   */
  public function paginationInfo(): array
  {
    return [
      'from' => $this->page * $this->limit - $this->limit + 1,
      'to' => $this->page * $this->limit > $this->quantity ? $this->quantity : $this->page * $this->limit,
      'of' => $this->quantity,
    ];
  }
}

class Log extends Logger
{
  /** @var Log */
  private static $logger;

  private $levels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

  /**
   * @inheritdoc
   */
  public function __construct(string $name, array $handlers = [], array $processors = [], DateTimeZone|null $timezone = null)
  {
    parent::__construct($name, $handlers, $processors, $timezone);
  }

  /**
   * @param  string                  $name
   * @param  array                   $handlers
   * @param  array                   $processors
   * @param  \DateTimeZone|null|null $timezone
   *
   * @return Log
   */
  public static function instance(string $name, array $handlers = [], array $processors = [], DateTimeZone|null $timezone = null): Log
  {
    if (empty(self::$logger)) {
      self::$logger = new self($name, $handlers, $processors, $timezone);
      self::$logger->pushHandler(new StreamHandler(__DIR__ . '/project.log', Level::Warning));
    }

    return self::$logger;
  }

  public function getLevels(): array
  {
    return self::$logger->levels;
  }

  /**
   * @param  string             $level
   * @param  string|\Stringable $message
   * @param  array              $context
   *
   * @return void
   */
  public static function pushLog(string $level, string|Stringable $message, array $context = [])
  {
    $level = strtolower($level);

    if (!in_array($level, self::$logger->getLevels())) $level = 'warning';

    self::$logger->$level($message, $context);
  }
}

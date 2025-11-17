<?php
namespace App\Helpers;

class Validator{
  protected array $data;
  protected array $errors = [];

  public function __construct(array $data)
  {
    $this->data = $data;
  }

  // validasi required
  public function required(array $fields): self
  {
    foreach($fields as $field){
      if(!isset($this->data[$field]) || trim($this->data[$field]) === ''){
        $this->errors[$field][] = "Field {$field} wajib diisi!";
      }
    }
    return $this;
  }

  // max length
  public function maxLength(string $field, int $length): self
  {
    if(isset($this->data[$field]) && mb_strlen($this->data[$field]) > $length){
      $this->errors[$field][] = "Field {$field} maksimal {$length} karakter.";
    }
    return $this;
  }

  // min length
  public function minLength(string $field, int $length): self
  {
    if(isset($this->data[$field]) && mb_strlen($this->data[$field]) < $length){
      $this->errors[$field][] = "Field {$field} minimal {$length} karakter.";
    }
    return $this;
  }

  // in list
  public function inList(string $field, array $list): self
  {
    if(isset($this->data[$field]) && !in_array($this->data[$field], $list)){
      $this->errors[$field][] = "Field {$field} tidak valid.";
    }
    return $this;
  }

  // sanitasi input
  public function sanitize(string $field): string
  {
    return isset($this->data[$field]) ? htmlspecialchars($this->data[$field], ENT_QUOTES, 'UTF-8') : '';
  }

  public function image(string $field, ?array $file = null): self
  {
    $file = $file ?? ($_FILES[$field] ?? null);
    if ($file && $file['error'] === UPLOAD_ERR_OK) {
      $errors = FileValidator::checkImage($file);
      if (!empty($errors)) {
        $this->errors[$field] = array_merge($this->errors[$field] ?? [], $errors);
      }
    }
    return $this;
  }

  public function multipleImages(string $field): self
  {
    if (!isset($_FILES[$field]) || empty($_FILES[$field]['name'][0])) return $this;

    foreach ($_FILES[$field]['name'] as $i => $name) {
      if ($_FILES[$field]['error'][$i] !== UPLOAD_ERR_OK) continue;
      $file = [
        'name' => $_FILES[$field]['name'][$i],
        'tmp_name' => $_FILES[$field]['tmp_name'][$i],
        'size' => $_FILES[$field]['size'][$i],
        'error' => $_FILES[$field]['error'][$i],
      ];
      $errors = FileValidator::checkImage($file);
      if (!empty($errors)) {
        $this->errors[$field][] = "File #".($i+1).": " . implode(', ', $errors);
      }
    }
    return $this;
  }

  // cek error
  public function hasErrors(): bool
  {
    return !empty($this->errors);
  }

  public function getErrors(): array
  {
    return $this->errors;
  }
}

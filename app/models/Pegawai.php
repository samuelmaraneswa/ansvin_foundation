<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Pegawai{
  private PDO $db;

  protected $table = 'users_pegawai';
  public function __construct()
  {
    $this->db = Database::connect();
  }

 public function getAll()
  {
    $sql = "
      SELECT 
        p.id,
        p.nip,
        p.nama,
        p.jabatan_id,
        j.nama AS nama_jabatan,
        p.unit_id,
        u.nama AS nama_unit,
        p.foto,
        p.created_at
      FROM pegawai p
      LEFT JOIN jabatan j ON p.jabatan_id = j.id
      LEFT JOIN unit_sekolah u ON p.unit_id = u.id
      ORDER BY p.created_at DESC
    ";

    return $this->db->query($sql)->fetchAll();
  }

  public function getById(int $id): ?array
  {
    $sql = "
        SELECT p.*, u.role
        FROM pegawai p
        LEFT JOIN users_pegawai u ON u.pegawai_id = p.id
        WHERE p.id = :id
        LIMIT 1
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id' => $id]);
    $pegawai = $stmt->fetch(\PDO::FETCH_ASSOC);

    return $pegawai ?: null; // kembalikan null jika tidak ditemukan
  }

  /**
   * Generate NIP baru
   * @param string $prefix Prefix NIP, misal "SYS", "TK", "SD"
   * @return string NIP baru
   */
  public function generateNIP(string $unitName): string
  {
    $year = date('y');

    // Ambil kata pertama dan hilangkan spasi
    $unitCode = strtoupper(strtok($unitName, ' '));
    $unitCode = str_replace(' ', '', $unitCode);

    $prefix = "ANV{$year}{$unitCode}";

    $stmt = $this->db->prepare("SELECT nip FROM pegawai WHERE nip LIKE :prefix ORDER BY id DESC LIMIT 1");
    $stmt->execute(['prefix' => $prefix . '%']);
    $last = $stmt->fetchColumn();

    if ($last) {
        preg_match('/(\d+)$/', $last, $matches);
        $number = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
    } else {
        $number = 1;
    }

    return sprintf("%s%04d", $prefix, $number);
  }

  public function insert(array $data)
  {
    $columns = array_keys($data);
    $placeholders = array_map(fn($col) => ':' . $col, $columns);

    $sql = "INSERT INTO pegawai (" . implode(',', $columns) . ")
            VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $this->db->prepare($sql);
    $stmt->execute($data);

    return $this->db->lastInsertId();
  }

  public function update(int $id, array $data)
  {
    $fields = [];
    foreach($data as $k => $v){
        $fields[] = "$k = :$k";
    }
    $sql = "UPDATE pegawai SET " . implode(',', $fields) . " WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $data['id'] = $id;
    return $stmt->execute($data);
  }

  public function delete($id)
  {
    $stmt = $this->db->prepare("DELETE FROM pegawai WHERE id = ?");
    return $stmt->execute([$id]);
  }

  public function searchPegawai($search = '', $unit_id = '', $limit = 5, $offset = 0)
  {
    $sql = "
        SELECT 
        p.id, p.nip, p.nama, u.nama AS nama_unit
        FROM pegawai p
        LEFT JOIN unit_sekolah u ON p.unit_id = u.id
        WHERE 1=1
    ";

    $params = [];

    // Filter nama
    if ($search !== '') {
        $sql .= " AND p.nama LIKE :search";
        $params[':search'] = "%$search%";
    }

    // Filter unit
    if ($unit_id !== '') {
        $sql .= " AND p.unit_id = :unit_id";
        $params[':unit_id'] = $unit_id;
    }

    // Urut dan batasi hasil
    $sql .= " ORDER BY p.nama ASC LIMIT :limit OFFSET :offset";

    $stmt = $this->db->prepare($sql);

    // Bind parameter dinamis
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    // Bind khusus untuk limit dan offset (harus tipe INT)
    $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function countPegawai($search = '', $unit_id = '')
  {
    $sql = "
      SELECT COUNT(*) AS total
      FROM pegawai p
      LEFT JOIN unit_sekolah u ON p.unit_id = u.id
      WHERE 1=1
    ";

    $params = [];

    if (!empty($search)) {
      $sql .= " AND p.nama LIKE :search";
      $params[':search'] = "%$search%";
    }

    if (!empty($unit_id)) {
      $sql .= " AND p.unit_id = :unit_id";
      $params[':unit_id'] = $unit_id;
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);

    return (int) $row['total'];
  }

  public function getAllPaginated($limit, $offset)
  {
    $sql = "
      SELECT 
          p.id, p.nip, p.nama, u.nama AS nama_unit
      FROM pegawai p
      LEFT JOIN unit_sekolah u ON p.unit_id = u.id
      ORDER BY p.nama ASC
      LIMIT :limit OFFSET :offset
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getByUnit($unit_id)
  {
    $sql = "SELECT p.*, u.nama AS nama_unit
            FROM pegawai p
            LEFT JOIN unit_sekolah u ON p.unit_id = u.id
            WHERE p.unit_id = :unit_id
            ORDER BY p.nama ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':unit_id', $unit_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getByUnitWithJabatanPaginated($unit_id, $limit, $offset)
  {
    $sql = "SELECT 
              p.id,
              p.nip,
              p.nama,
              j.nama AS nama_jabatan,
              u.nama AS nama_unit
            FROM pegawai p
            LEFT JOIN unit_sekolah u ON p.unit_id = u.id
            LEFT JOIN jabatan j ON p.jabatan_id = j.id
            WHERE p.unit_id = :unit_id
            ORDER BY p.id DESC LIMIT :limit OFFSET :offset";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':unit_id', $unit_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function findByIdAndUnit(int $id, int $unit_id): ?array
  {
    $sql = "
        SELECT 
            p.id,
            p.nip,
            p.nama,
            p.unit_id,
            p.jabatan_id,
            j.nama AS nama_jabatan,
            p.email,
            p.telepon,
            p.tanggal_lahir,
            p.alamat,
            p.status_aktif,
            p.foto
        FROM pegawai p
        LEFT JOIN jabatan j ON p.jabatan_id = j.id
        WHERE p.id = :id AND p.unit_id = :unit_id
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':unit_id' => $unit_id
    ]);

    $pegawai = $stmt->fetch(PDO::FETCH_ASSOC);
    return $pegawai ?: null;
  }

  public function searchByUnitPaginated($unit_id, $keyword, $limit, $offset)
  {
    $stmt = $this->db->prepare("
        SELECT 
            p.id,
            p.nama,
            p.nip,
            j.nama AS nama_jabatan
        FROM pegawai AS p
        LEFT JOIN jabatan AS j ON p.jabatan_id = j.id
        WHERE p.unit_id = :unit_id
          AND p.nama LIKE :keyword
        ORDER BY p.nama ASC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':unit_id', $unit_id, PDO::PARAM_INT);
    $stmt->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function countByUnit($unit_id)
  {
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM pegawai WHERE unit_id = :unit_id");
    $stmt->execute([':unit_id' => $unit_id]);
    return $stmt->fetchColumn();
  }

  // Hitung total hasil pencarian
  public function countSearchByUnit($unit_id, $keyword) 
  {
    $stmt = $this->db->prepare("
        SELECT COUNT(*) 
        FROM pegawai 
        WHERE unit_id = :unit_id
          AND nama LIKE :keyword
    ");
    $stmt->execute([
        ':unit_id' => $unit_id,
        ':keyword' => "%$keyword%",
    ]);

    return $stmt->fetchColumn();
  }

}

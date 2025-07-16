<?php
  include 'config/connexion.php';

  session_start();

  $items_per_page = 5;
  $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
  if ($page < 1) $page = 1;

  $offset = ($page - 1) * $items_per_page;
  $buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

  try {
      if ($buscar !== '') {
          $totalStmt = $conexion->prepare("SELECT COUNT(*) FROM libros WHERE estado = 'activo' AND titulo LIKE :buscar");
          $totalStmt->bindValue(':buscar', "%$buscar%", PDO::PARAM_STR);
          $totalStmt->execute();
          $totalItems = (int) $totalStmt->fetchColumn();

          $totalPages = (int) ceil($totalItems / $items_per_page);

          $stmt = $conexion->prepare("SELECT * FROM libros WHERE estado = 'activo' AND titulo LIKE :buscar ORDER BY fecha_registro DESC LIMIT :limit OFFSET :offset");
          $stmt->bindValue(':buscar', "%$buscar%", PDO::PARAM_STR);
      } else {
          $totalStmt = $conexion->query("SELECT COUNT(*) FROM libros WHERE estado = 'activo'");
          $totalItems = (int) $totalStmt->fetchColumn();
          $totalPages = (int) ceil($totalItems / $items_per_page);

          $stmt = $conexion->prepare("SELECT * FROM libros WHERE estado = 'activo' ORDER BY fecha_registro DESC LIMIT :limit OFFSET :offset");
      }

      $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
      $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
      $stmt->execute();
      $libros = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
      die("Error al obtener libros: " . $e->getMessage());
  }

  
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Biblioteca Escolar</title>
  <link rel="shortcut icon" href="img/logo-biblioteca.PNG" type="image/x-icon">

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.css" rel="stylesheet" />
</head>
<body>

<?php include 'includes/header.php'; ?>


<main class="max-w-6xl mx-auto py-10 px-4">
<form method="GET" action="" class="max-w-sm mx-auto mb-6 flex">
  <input
    type="text"
    name="buscar"
    placeholder="Buscar libro por título"
    value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>"
    class="flex-grow px-4 py-2 border rounded-l focus:outline-none focus:ring-2 focus:ring-blue-500"
  />
  <button
    type="submit"
    class="px-4 py-2 bg-blue-600 text-white rounded-r hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
  >
    Buscar
  </button>
</form>

<h1 class="text-lg md:text-xl font-semibold text-white bg-blue-600 px-6 py-2 rounded-md text-center shadow-sm mb-3">
  Listado de Libros
</h1>

  <?php if (count($libros) === 0): ?>
      <p class="text-center text-gray-600">No hay libros disponibles.</p>
    <?php else: ?>
      <div class="grid gap-6">
        <?php foreach ($libros as $libro): ?>
          <div class="bg-white rounded-xl shadow-md overflow-hidden flex flex-col md:flex-row">
            <img src="img/portadas-libros/<?= htmlspecialchars($libro['imagen']) ?>" alt="Portada del libro" class="w-32 h-40 object-cover rounded-md m-4" onerror="this.onerror=null;this.src='img/portada.svg';"/>
            <div class="p-6 flex-1">
              <h2 class="text-2xl font-semibold text-gray-800"><?= htmlspecialchars($libro['titulo']) ?></h2>
              <p class="text-gray-600 mt-1">Autor: <?= htmlspecialchars($libro['autor']) ?> | Año: <?= $libro['anio'] ?></p>
              <?php if (!empty($libro['descripcion'])): ?>
                <p class="text-gray-500 mt-4 text-sm"><?= substr(htmlspecialchars($libro['descripcion']), 0, 400) ?>...</p>
              <?php endif; ?>
              <p class="text-sm text-green-600 mt-2">Disponibles: <?= $libro['cantidad_disponible'] ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Paginación -->
  <div class="mt-8 flex justify-center space-x-2">
    <?php if ($page > 1): ?>
      <!-- Enlace a la página anterior, conservando el parámetro 'buscar' -->
      <a href="?page=<?= $page - 1 ?>&buscar=<?= urlencode($buscar) ?>" 
        class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">
        Anterior
      </a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <!-- Enlaces a las páginas, marcando la página actual con color distinto -->
        <a href="?page=<?= $i ?>&buscar=<?= urlencode($buscar) ?>" 
          class="px-3 py-1 rounded <?= $i === $page ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>

      <?php if ($page < $totalPages): ?>
        <!-- Enlace a la página siguiente, conservando el parámetro 'buscar' -->
        <a href="?page=<?= $page + 1 ?>&buscar=<?= urlencode($buscar) ?>" 
          class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300">
          Siguiente
        </a>
      <?php endif; ?>
    </div>

    <?php endif; ?>
  </main>


<?php include 'includes/footer.php'; ?>


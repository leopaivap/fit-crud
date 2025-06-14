<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Exercícios - FitCrud</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <?php
  include_once 'auth-guard.php';

  include_once "./database/connection.php";

  $canManage = in_array($_SESSION['user_role'], ['admin', 'trainer']);

  $id = $_GET['id'] ?? null;
  $editing = false;

  // Define os valores padrão do formulário
  $exercise = [
    'name' => '',
    'exercise_type' => '',
    'description' => '',
    'muscle_group_id' => '',
    'difficulty' => 'beginner'
  ];

  // Se estiver editando, busca os dados do exercício
  if ($id) {
    $stmt = $connection->prepare("SELECT * FROM exercises WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $exerciseData = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($exerciseData) {
      $exercise = $exerciseData;
      $editing = true;
    }
  }

  // Carrega os grupos musculares
  $muscleQuery = "SELECT id, name FROM muscle_groups ORDER BY name ASC";
  $muscleStmt = $connection->prepare($muscleQuery);
  $muscleStmt->execute();
  $muscleGroups = $muscleStmt->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <?php include_once 'templates/navbar.php'; ?>


  <main class="container mt-5">
    <h2 class="mb-4">
      <?= $editing ? 'Editando exercício "' . htmlspecialchars($exercise['name']) . '"' : "Cadastrar Exercício" ?>
    </h2>

    <?php
    session_start();
    if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])) {
      echo '<div class="alert alert-danger" role="alert"><ul>';
      foreach ($_SESSION['errors'] as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
      }
      echo '</ul></div>';
      unset($_SESSION['errors']);
    }
    ?>


    <form id="exerciseForm" class="row g-3"
      action="./database/exercise/<?= $editing ? 'update-exercise.php?id=' . $id : 'insert-exercise.php' ?>"
      method="POST">

      <div class="col-md-6">
        <label for="exercise_name" class="form-label">Nome do Exercício:</label>
        <input type="text" class="form-control" id="exercise_name" name="exercise_name"
          value="<?= htmlspecialchars($exercise['name']) ?>" required>
      </div>
      <div class="col-md-6">
        <label for="exercise_type" class="form-label">Tipo de Exercício:</label>
        <input type="text" class="form-control" id="exercise_type" name="exercise_type"
          value="<?= htmlspecialchars($exercise['exercise_type']) ?>" required>
      </div>
      <div class="col-md-6">
        <label for="description" class="form-label">Descrição:</label>
        <input type="text" class="form-control" id="description" name="description"
          value="<?= htmlspecialchars($exercise['description']) ?>" required>
      </div>

      <div class="col-md-6">
        <label for="muscle_group" class="form-label">Grupo Muscular:</label>
        <select name="muscle_group" id="muscle_group" class="form-control" required>
          <option value="">Selecione um grupo muscular</option>
          <?php foreach ($muscleGroups as $group): ?>
            <option value="<?= $group['id'] ?>" <?= $group['id'] == $exercise['muscle_group_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($group['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>


      <div class="col-md-6">
        <label for="difficulty" class="form-label">Dificuldade</label>
        <select class="form-select" id="difficulty" name="difficulty">
          <option value="beginner" <?= $exercise['difficulty'] == 'beginner' ? 'selected' : '' ?>>Iniciante</option>
          <option value="intermediate" <?= $exercise['difficulty'] == 'intermediate' ? 'selected' : '' ?>>Intermediário
          </option>
          <option value="advanced" <?= $exercise['difficulty'] == 'advanced' ? 'selected' : '' ?>>Avançado</option>
        </select>

      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-dark"><?= $editing ? 'Salvar' : 'Cadastrar' ?></button>
      </div>
    </form>

    <hr class="my-5" />

    <?php

    ob_start();

    include_once "./database/connection.php";

    ob_end_clean();


    $query = "
    SELECT 
        exercises.*, 
        muscle_groups.name AS muscle_group_name 
    FROM exercises 
    JOIN muscle_groups ON exercises.muscle_group_id = muscle_groups.id
";
    $stmt = $connection->prepare($query);
    $stmt->execute();

    $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);

    ?>

    <h3>Exercícios Cadastrados</h3>

    <table class="table table-hover mt-3">
      <thead class="table-dark">
        <tr>
          <th>Nome</th>
          <th>Tipo</th>
          <th>Grupo Muscular</th>
          <th>Dificuldade</th>
          <th>Descrição</th>
          <?php if ($canManage): ?>
            <th>Ações</th> <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php
        // Verifica se existem exercícios cadastrados
        if ($exercises) {
          foreach ($exercises as $exercise) {
            // Para cada exercício, exibe os dados
            echo "<tr>";
            echo "<td>" . htmlspecialchars($exercise['name']) . "</td>";
            echo "<td>" . htmlspecialchars($exercise['exercise_type']) . "</td>";

            // Carrega o nome do grupo muscular
            echo "<td>" . htmlspecialchars($exercise['muscle_group_name']) . "</td>";

            if ($exercise['difficulty'] == "beginner") {
              $difficultyText = "Iniciante";
            } elseif ($exercise['difficulty'] == "intermediate") {
              $difficultyText = "Intermediário";
            } else {
              $difficultyText = "Avançado";
            }
            echo "<td>" . htmlspecialchars($difficultyText) . "</td>";
            echo "<td>" . htmlspecialchars($exercise['description']) . "</td>";
            if ($canManage) {
              echo "<td><a href='exercises.php?id=" . $exercise['id'] . "'>Editar</a> | <a href='./database/exercise/delete-exercise.php?id=" . $exercise['id'] . "'>Excluir</a></td>";
            }
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='6'>Nenhum exercício cadastrado</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </main>

  <div id="footer"></div>
  <script src="js/footer.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/exercise-validator.js"></script>

</body>

</html>
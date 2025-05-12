document.getElementById("navbar").innerHTML = `
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
  <a class="navbar-brand text-orange" href="index.php">FitCrud</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navLinks">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navLinks">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link" href="workouts.php">Treinos</a></li>
      <li class="nav-item"><a class="nav-link" href="exercises.php">Exercícios</a></li>
      <li class="nav-item"><a class="nav-link" href="login.php">Logout</a></li>
    </ul>
  </div>
</nav>
`;

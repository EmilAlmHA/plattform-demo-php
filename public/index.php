<?php
// A small notes app for the student platform demo. The platform sets
// PGHOST, PGDATABASE, PGUSER and PGPASSWORD when the project has a database.
$db = new PDO(
    "pgsql:host=" . getenv("PGHOST") . ";dbname=" . getenv("PGDATABASE"),
    getenv("PGUSER"),
    getenv("PGPASSWORD"),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $text = trim($_POST["text"] ?? "");
    if ($text !== "") {
        $db->prepare("INSERT INTO notes (text) VALUES (?)")->execute([mb_substr($text, 0, 200)]);
    }
    // Relative redirect, so it works under /<project>/ on the platform.
    header("Location: ./", true, 303);
    exit;
}

$notes = $db->query("SELECT text, created_at FROM notes ORDER BY id DESC LIMIT 20")->fetchAll();
?>
<!doctype html>
<title>Demo PHP</title>
<h1>Anteckningar (PHP + PostgreSQL)</h1>
<form method="post" action="./"><input name="text" required maxlength="200"> <button>Spara</button></form>
<ul>
<?php foreach ($notes as $n): ?>
    <li><?= htmlspecialchars($n["text"]) ?> <small><?= substr($n["created_at"], 0, 16) ?></small></li>
<?php endforeach ?>
</ul>

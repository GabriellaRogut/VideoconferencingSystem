<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignConnect - Докладване на нарушение</title>

    <?php include("includes/links.php"); ?>
  
    <style>
      header {
        background-color: var(--header-txt-call);
        color: var(--white);
        padding: 2rem;
        text-align: center;
        box-shadow: 0 2px 6px #00000026;
      }

      header h1 {
        font-size: 2rem;
        font-weight: 600;
      }

      main {
        max-width: 750px;
        margin: 2rem auto;
        background: var(--color-white);
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 6px 18px #0000001a;
        border: 1px solid #e0e0e0;
      }

      h2 {
        color: var(--input-border);
        margin-top: 1.5rem;
        font-family: "Montserrat", sans-serif;
      }

      p {
        line-height: 1.6;
        margin: 0.7rem 0;
      }

      ul {
        margin: 0.5rem 0 1rem 1.5rem;
      }

      li {
        margin: 0.7rem 0;
      }

      a.button {
        display: inline-block;
        margin-top: 1.5rem;
        padding: 0.75rem 1.5rem;
        background: var(--gradient-bg);
        color: var(--color-white);
        border-radius: 12px;
        border: none;
        font-weight: 600;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      }

      a.button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0,0,0,0.15);
      }

      .violation-box {
        background-color: #ffe6e6;
        border: 1px solid #ff4d4f;
        color: #a60000;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
      }

      .violation-box i {
        font-weight: bold;
        font-size: 1.3rem;
      }
    </style>
</head>


<body>
  <header>
    <h1>Докладване на нарушение</h1>
  </header>

  <main>
    <div class="violation-box">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <span>Съобщението, което се опитвате да изпратите, нарушава правилата на платформата. Моля, прочетете внимателно указанията по-долу, за да разберете защо е блокирано и как да постъпите.</span>
    </div>

    <p>В платформата SignConnect се стремим да осигурим безопасна, уважителна и приобщаваща среда за всички участници. Всяко съобщение, което съдържа неподходящо или обидно съдържание, ще бъде блокирано автоматично, за да се защити общността.</p>

    <h2>Примери за забранени действия</h2>
    <ul>
      <li>Използване на обидни, расистки, сексистки или друг вид неприемливи думи и изрази.</li>
      <li>Споделяне на съдържание с насилие, омраза, дискриминация или заплахи.</li>
      <li>Опити за спам, фишинг, измами или изпращане на злонамерени линкове.</li>
      <li>Публикуване на лични данни на други лица без тяхното разрешение.</li>
      <li>Опити за нарушаване на правилата на платформата чрез заобикаляне на филтри за съдържание.</li>
    </ul>

    <h2>Как да постъпите</h2>
    <p>Ако вашето съобщение е блокирано:</p>
    <ul>
      <li>Прегледайте внимателно съдържанието и премахнете неподходящите думи или линкове.</li>
      <li>Съставете ново съобщение, което съответства на правилата за използване.</li>
      <li>Ако смятате, че съобщението е блокирано по погрешка, можете да се свържете с администратора за съдействие.</li>
    </ul>

    <p>Неспазването на тези правила може да доведе до временно или постоянно ограничаване на достъпа до чата или други функционалности.</p>

    <a href="mailto:admin@signconnect.bg" class="button">Свържи се с администратор</a>
  </main>

  <footer>
      © 2025 SignConnect. Всички права запазени.
      <a href="#" class="admin-entry" id="openAdminModal">System</a>
  </footer>
</body>
</html>

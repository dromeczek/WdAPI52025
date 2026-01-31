# ZASIANE 🌱  
Aplikacja webowa do zarządzania nawykami w formie wirtualnego ogrodu

## 📌 Opis projektu
ZASIANE to aplikacja webowa umożliwiająca użytkownikom tworzenie i zarządzanie nawykami,
które wizualizowane są w postaci roślin. Regularne wykonywanie nawyków powoduje „wzrost”
roślin, natomiast brak aktywności prowadzi do ich obumierania.

Aplikacja posiada system logowania, role użytkowników oraz panel administracyjny
umożliwiający zarządzanie kontami.

---

## 🛠️ Wykorzystane technologie
- **Backend:** PHP (programowanie obiektowe, architektura MVC)
- **Frontend:** HTML5, CSS3, JavaScript
- **AJAX:** Fetch API
- **Baza danych:** PostgreSQL
- **Konteneryzacja:** Docker / Docker Compose
- **Kontrola wersji:** Git

---

## 🧱 Architektura aplikacji
Projekt oparty jest na architekturze **MVC (Model–View–Controller)**:
- **Model:** Repozytoria (dostęp do bazy danych)
- **View:** Widoki HTML
- **Controller:** Kontrolery obsługujące logikę aplikacji
- **Routing:** Własny mechanizm routingu

Backend został napisany w sposób obiektowy (OOP).

---

## 👤 Role użytkowników
W aplikacji występują co najmniej dwie role:
- **USER** – standardowy użytkownik aplikacji
- **ADMIN** – administrator z dostępem do panelu administracyjnego

Uprawnienia są egzekwowane po stronie backendu.

---

## 🔐 Bezpieczeństwo
- Hasła przechowywane są w postaci zahashowanej (`password_hash`)
- Zapytania do bazy realizowane są przez **prepared statements**
- System sesji użytkownika
- Blokada dostępu dla zbanowanych użytkowników
- Autoryzacja dostępu do panelu administratora
- Walidacja danych wejściowych

---

## 🗄️ Baza danych
Baza danych została zaprojektowana w PostgreSQL i zawiera m.in.:
- relacje 1:N
- klucze główne i obce
- ograniczenia `UNIQUE`
- mechanizmy `ON DELETE CASCADE`

### Diagram ERD
Diagram ERD znajduje się w pliku:
<img width="1229" height="599" alt="image" src="https://github.com/user-attachments/assets/d7e3a200-1905-4486-9f06-f126a04facad" />


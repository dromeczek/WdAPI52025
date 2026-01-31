# ZASIANE 🌱
Aplikacja webowa do zarządzania nawykami w formie wirtualnego ogrodu.

## Opis projektu
**ZASIANE** to aplikacja webowa umożliwiająca użytkownikom tworzenie i zarządzanie nawykami,  
które są wizualizowane w postaci roślin. Regularne wykonywanie nawyków powoduje „wzrost” roślin,  
natomiast brak aktywności prowadzi do ich obumierania.

Dodatkowo aplikacja posiada **system kar (HP)** zależny od częstotliwości wykonywania nawyku:

| Częstotliwość | Kara za 1 dzień zwłoki | Dni do uschnięcia |
|---|---:|---:|
| 7/7 (Codziennie) | 15 HP | ~7 dni |
| 3–5/7 (Często) | 10 HP | 10 dni |
| 1/7 (Rzadko) | 5 HP | 20 dni |


Aplikacja posiada system logowania, role użytkowników oraz panel administracyjny umożliwiający zarządzanie kontami.

---

## Funkcje
- Rejestracja i logowanie użytkowników
- Tworzenie i zarządzanie nawykami
- Wizualizacja nawyków jako „roślin” (wzrost / obumieranie)
- System kar (HP) zależny od częstotliwości nawyku
- Role użytkowników: **USER** i **ADMIN**
- Panel administratora do zarządzania kontami (np. blokady / role)

---

## Wykorzystane technologie
- **Backend:** PHP (OOP, architektura MVC)
- **Frontend:** HTML5, CSS3, JavaScript
- **AJAX:** Fetch API
- **Baza danych:** PostgreSQL
- **Konteneryzacja:** Docker / Docker Compose
- **Kontrola wersji:** Git

---

## Architektura aplikacji
Projekt oparty jest na architekturze **MVC (Model–View–Controller)**:
- **Model:** repozytoria (dostęp do bazy danych)
- **View:** widoki HTML
- **Controller:** kontrolery obsługujące logikę aplikacji
- **Routing:** własny mechanizm routingu

Backend został napisany w sposób obiektowy (OOP).

---

## Role użytkowników
W aplikacji występują co najmniej dwie role:
- **USER** – standardowy użytkownik aplikacji
- **ADMIN** – administrator z dostępem do panelu administracyjnego

Uprawnienia są egzekwowane po stronie backendu.

---

## Bezpieczeństwo
- Hasła przechowywane w postaci zahashowanej (`password_hash`)
- Zapytania do bazy realizowane przez **prepared statements**
- System sesji użytkownika
- Blokada dostępu dla zbanowanych użytkowników
- Autoryzacja dostępu do panelu administratora
- Walidacja danych wejściowych

---

## Baza danych
Baza danych została zaprojektowana w PostgreSQL i zawiera m.in.:
- relacje 1:N
- klucze główne i obce
- ograniczenia `UNIQUE`
- mechanizmy `ON DELETE CASCADE`
- trigger / funkcje wspierające logikę (np. automatyczne czyszczenie danych po banie)

### Diagram ERD
Diagram ERD znajduje się poniżej:

<img width="1092" height="555" alt="image" src="https://github.com/user-attachments/assets/7510b414-db5e-4f79-a731-07127559c6a0" />


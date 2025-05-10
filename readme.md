# 🍔 BurgerPick - 커뮤니티 쿠폰 추첨기

**BurgerPick**은 닉네임 언급이 제한된 커뮤니티 환경에서 쿠폰(햄버거 등)을 공정하게 배포하기 위한 추첨 시스템입니다. AMP 환경(Apache, MariaDB, PHP)에서 작동하며, 보안성과 사용 편의성에 중점을 둔 PHP 웹 애플리케이션입니다.

---

## ✅ 주요 기능

* **쿠폰 등록 및 삭제** - 관리자 페이지에서 쿠폰 직접 추가 가능
* **비밀번호 기반 쿠폰 확인** - 당첨자는 4자리 숫자로 본인 확인 후 쿠폰 열람
* **관리자 승인 기반 노출** - 승인된 사용자만 쿠폰 및 바코드 확인 가능
* **바코드 생성** - Picqer 라이브러리를 활용한 쿠폰 바코드 이미지 출력
* **AES-256 암호화** - 사용자 입력 비밀번호는 안전하게 저장됨

---

## 🧱 시스템 요구사항

* PHP 7.4 이상 (GD 확장 활성화 필수)
* Apache 2.x
* MariaDB / MySQL
* Composer

---

## ⚙️ 설치 방법

### 1. 프로젝트 복제 및 이동

```bash
git clone https://github.com/yourname/burgerpick.git
cd burgerpick
```

### 2. Composer 의존성 설치

```bash
composer install
```

### 3. DB 구성

```bash
mysql -u root -p < schema.sql
```

> 데이터베이스 이름: `burger_coupon`

### 4. Apache 설정 (선택)

```
DocumentRoot "C:/xampp/htdocs/burgerpick"
```

---

## 🔑 관리자 로그인 정보

* ID: `Kimchi`
* PW: `Danmuji`

---

## 🗃️ 디렉토리 구조

```
burgerpick/
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── save_coupon.php
│   ├── delete_coupon.php
│   ├── process_coupon.php
│   └── logout.php
├── index.php
├── barcode.php
├── db.php
├── functions.php
├── schema.sql
└── README.md
```

---

## 🛡 보안 참고

* 모든 DB 쿼리는 **Prepared Statement**로 SQL Injection 방지
* 비밀번호는 AES-256-CBC 방식으로 암호화되어 저장됨
* 쿠폰 접근은 **token 기반 링크**와 **비밀번호 입력**으로 이중 보호됨

---

## 🧪 테스트 URL 예시

```text
http://localhost/burgerpick/index.php?token=1_f4e2a7c1
```

---

## 🐞 문제 해결

* 바코드가 보이지 않는 경우:

  * `barcode.php`는 반드시 **UTF-8 without BOM**으로 저장돼야 합니다
  * PHP GD 모듈이 활성화되어 있어야 합니다 (`php.ini`에서 `extension=gd` 확인)

---

## 📄 라이선스

MIT License

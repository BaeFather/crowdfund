## 프로젝트 개요

P2P 금융 크라우드펀딩 플랫폼.
투자자와 차주를 연결하는 온라인 투자 플랫폼으로, 금융위원회 등록 P2P 금융업체.

서버 배포 경로: `/home/crowdfund/public_html/`  
로컬 작업 경로: `public_html/`
**그누보드5(Gnuboard 5.2.0)** 기반 PHP 레거시 웹사이트.

## 핵심 아키텍처

### 프레임워크
- **그누보드5** 기반 PHP 웹앱 (MVC 없음, 페이지 단위 PHP 파일 구조)
- `config.php` → `common.php` → 각 페이지 `_common.php` → 페이지 본문 순서로 초기화
- `config.php`에서 모든 경로 상수(`G5_PATH`, `G5_URL` 등) 및 서비스 설정 정의
- `config.service.php`에서 전용 서비스 설정 관리 (관리자 서버에서 편집 후 crontab으로 서버 간 rsync)

### 테마 시스템
- 활성 테마: `public_html/theme/2018/` (PC용 + 모바일용)
- PC 레이아웃: `theme/2018/head.php`, `theme/2018/tail.php`, `theme/2018/head.sub.php`, `theme/2018/tail.sub.php`
- 모바일 레이아웃: `theme/2018/mobile/` 하위
- 각 기능별 디렉토리마다 `_head.php`, `_tail.php`, `_head.sub.php`, `_tail.sub.php` 존재 (테마 파일 위임)
- `G5_SET_DEVICE = 'both'`: 접속 디바이스에 따라 PC/모바일 화면 자동 전환

### 주요 디렉토리별 기능
| 디렉토리 | 기능 |
|---------|------|
| `investment/` | 투자상품 목록(`invest_list.php`), 상품 상세(`detail.php`, `investment.php`), 투자 처리(`investment_proc.php`), AJAX 실시간 투자(`ajax_investment.php`) |
| `deposit/` | 투자 내역/예치금, 가상계좌 연동, INIpay50 결제 모듈 |
| `mypage/` | 마이페이지 (예치금, 투자내역, 회원정보 수정) |
| `loan/` | 대출 신청 (`loan.php`, `loan_proc.php`) |
| `auto_invest/` | 자동투자 설정 |
| `AML/` | KYC/AML(자금세탁방지) 처리, WLF(워치리스트 필터링) |
| `p2pctr/` | 한국P2P금융협회 중앙기록관리시스템 연동 |
| `bank_account/` | 은행계좌 인증 |
| `api/` | 내부 API (회원 인증, 이메일/전화번호 중복체크) |
| `adm/` | 관리자 기능 |
| `lib/` | 공용 라이브러리 함수 |
| `member/`, `member_new/` | 회원가입, 로그인 |
| `bbs/` | 그누보드 게시판 |

### 핵심 라이브러리 (`lib/`)
- `common.lib.php` - 공통 유틸리티 함수
- `investment.lib.php` - 투자 처리 핵심 로직
- `product.lib.php` - 상품 목록 캐시 처리 (`getProductList()`)
- `repay_calculation.php` / `repay_calculation_new.php` - 원리금 상환 계산
- `p2pctr_svc.lib.php` - P2P 중앙기록관리 API 연동 (`https://openapi.p2pcenter.or.kr/v1.0/`)
- `sms.lib.php` - SMS 발송 (별도 DB: `link3`로 접속)
- `mailer.lib.php` - SMTP 이메일 발송 (PHPMailer, `hello.hellofunding.co.kr:25`)
- `crypt.lib.php` - 암호화/복호화 (계좌번호 등 민감정보)
- `hyphen.lib.svc.php` - 하이픈 본인인증 API 연동
- `insidebank.lib.php` - 인사이드뱅크(신한은행) 연동
- `nujuk_state.lib.php` - 누적 투자 현황 처리
- `invest_queue.lib.php` - 투자 큐 처리

### 핵심 DB 테이블
- `cf_product` - 투자 상품 (A 테이블로 자주 사용)
- `cf_product_container` - 상품 상세 (B 테이블로 조인)
- `cf_product_invest` - 투자 내역 (`invest_state='Y'` 가 성공 투자)
- `g5_member` - 회원 정보 (그누보드 표준 + 확장 필드)
- `vacs_ahst`, `vacs_vact` - 가상계좌 이력/목록 (신한은행 가상계좌)
- `g5_sms_admininfo`, `g5_sms_userinfo` - SMS 발송 템플릿

### 외부 연동 API
- `ext.hellofunding.co.kr` - 내부 확장 API 서버 (`API_URL` 상수)
- `openapi.p2pcenter.or.kr` - P2P 중앙기록관리 (금융위원회 P2PCTR)
- 하이픈(Hyphen) - 본인인증
- 신한은행(InsideBank) - 가상계좌/대출 실행
- INIpay50 - 결제

### 보안 및 입력 처리
- 모든 페이지 상단: `while( list($k, $v) = each($_REQUEST) ) { ${$k} = addslashes(clean_xss_tags(trim($v))); }` 패턴으로 입력 처리
- `G5_DISPLAY_SQL_ERROR = FALSE` (운영환경 SQL 에러 비노출)
- `G5_ESCAPE_FUNCTION = 'sql_escape_string'` 사용
- SQL 주입 차단 및 오피스 IP 접근 제어(`common.php`)
- 민감 정보(계좌번호)는 `crypt.lib.php`로 암호화

### 서버 환경
- 웹서버: LB(211.56.4.49) → www1(10.22.160.29), www2(10.22.160.125) 구성
- 세션 디렉토리: `data/session/` (도메인별 별도)
- 상품 목록 캐시: `data/cache/productList-{active|popular|latest}.php` (60초 TTL)
- MySQL: `G5_MYSQLI_USE = true` (MySQLi 드라이버 사용), SMS용 별도 DB(`link3`)

### 모바일 대응
- `G5_IS_MOBILE` 상수로 모바일 감지 (`G5_MOBILE_AGENT` 정규식 사용)
- 모바일 전용 파일: `*.m.php` 또는 `mobile/` 디렉토리
- 모바일 스킨: `theme/2018/mobile/skin/`

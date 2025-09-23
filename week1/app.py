from flask import Flask, request, render_template, redirect, url_for, flash
import sqlite3
from datetime import datetime
import os

# -----------------------
# Flask 앱 생성 (template_folder 지정 추가)
# -----------------------
BASE_DIR = os.path.dirname(__file__)
app = Flask(
    __name__,
    template_folder=os.path.join(BASE_DIR, 'templates'),  # index.html 있는 폴더
    static_folder=os.path.join(BASE_DIR, 'static')        # 필요없으면 지워도 됨
)
app.secret_key = 'dev'

DB_FILE = os.path.join(BASE_DIR, 'guestbook.db')

# -----------------------
# DB 초기화 함수
# -----------------------
def init_db():
    """guestbook.db와 guestbook 테이블을 자동으로 생성한다."""
    conn = sqlite3.connect(DB_FILE)
    cur = conn.cursor()
    cur.execute("""
    CREATE TABLE IF NOT EXISTS guestbook (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        message TEXT NOT NULL,
        created_at TEXT
    )
    """)
    conn.commit()
    conn.close()

# -----------------------
# DB 연결 함수
# -----------------------
def open_db_conn():
    conn = sqlite3.connect(DB_FILE)
    conn.row_factory = sqlite3.Row
    return conn

# -----------------------
# 메인 페이지 ('/')
# -----------------------
@app.route('/')
def index():
    db = open_db_conn()
    entries = db.execute('SELECT * FROM guestbook ORDER BY id DESC').fetchall()
    db.close()
    return render_template('index.html', entries=entries, q='', search_mode=False)

# -----------------------
# 검색 기능 ('/search')
# -----------------------
@app.route('/search')
def search():
    q = request.args.get('q', '')
    db = open_db_conn()
    rows = db.execute(
        "SELECT * FROM guestbook WHERE name LIKE ? OR message LIKE ? ORDER BY id DESC",
        (f'%{q}%', f'%{q}%')
    ).fetchall()
    db.close()
    return render_template('index.html', entries=rows, q=q, search_mode=True)

# -----------------------
# 글 작성 기능 ('/write')
# -----------------------
@app.route('/write', methods=['POST'])
def write():
    name = request.form.get('name','').strip()
    message = request.form.get('message','').strip()
    if not name or not message:
        flash("이름과 메시지를 입력해주세요.", 'error')
        return redirect(url_for('index'))

    created_at = datetime.utcnow().isoformat()
    db = open_db_conn()
    db.execute(
        'INSERT INTO guestbook (name, message, created_at) VALUES (?, ?, ?)',
        (name, message, created_at)
    )
    db.commit()
    db.close()
    return redirect(url_for('index'))

# -----------------------
# 메인 실행부
# -----------------------
if __name__ == '__main__':
    init_db()  # 서버 시작할 때 DB와 테이블 자동 생성
    app.run(debug=True)
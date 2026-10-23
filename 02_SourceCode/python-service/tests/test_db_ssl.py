"""ssl_connect_args: tham số TLS cho PyMySQL theo biến môi trường."""
from app.db import ssl_connect_args


def test_returns_empty_when_ssl_disabled():
    assert ssl_connect_args({}) == {}
    assert ssl_connect_args({"DB_SSL": "false"}) == {}


def test_requires_tls_without_ca():
    assert ssl_connect_args({"DB_SSL": "true"}) == {"ssl": {"ssl_mode": "REQUIRED"}}


def test_uses_ca_file_when_given():
    args = ssl_connect_args({"DB_SSL": "1", "DB_SSL_CA": "/srv/ca.pem"})
    assert args == {"ssl": {"ca": "/srv/ca.pem"}}

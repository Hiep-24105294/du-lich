<?php

namespace Tests\Unit;

use PDO;
use Tests\TestCase;

/**
 * Tùy chọn TLS của kết nối MySQL đọc từ env (MySQL đám mây như Aiven bắt buộc SSL).
 */
class DatabaseSslConfigTest extends TestCase
{
    private function loadMysqlOptions(): array
    {
        $config = require __DIR__.'/../../config/database.php';

        return $config['connections']['mysql']['options'];
    }

    protected function tearDown(): void
    {
        putenv('MYSQL_ATTR_SSL_CA');
        putenv('MYSQL_ATTR_SSL_VERIFY_SERVER_CERT');
        parent::tearDown();
    }

    public function test_no_tls_options_when_env_is_empty(): void
    {
        $this->assertSame([], $this->loadMysqlOptions());
    }

    public function test_ca_and_verify_false_are_passed_to_pdo(): void
    {
        putenv('MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt');
        putenv('MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=false');

        $options = $this->loadMysqlOptions();

        $this->assertSame('/etc/ssl/certs/ca-certificates.crt', $options[PDO::MYSQL_ATTR_SSL_CA]);
        $this->assertFalse($options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]);
    }

    public function test_verify_true_is_kept(): void
    {
        putenv('MYSQL_ATTR_SSL_CA=/tmp/ca.pem');
        putenv('MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=true');

        $options = $this->loadMysqlOptions();

        $this->assertTrue($options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]);
    }
}


<?php

test('halaman utama mengarahkan pengunjung ke halaman cek resi', function () {
    $response = $this->get('/');

    $response->assertRedirect('/cek-resi');
});

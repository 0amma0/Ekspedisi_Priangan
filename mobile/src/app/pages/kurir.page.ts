
import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import {
  IonContent,
  IonItem,
  IonLabel,
  IonInput,
  IonTextarea,
  IonButton,
  IonBadge,
  IonList
} from '@ionic/angular';

import { App } from '@capacitor/app';
import { PluginListenerHandle } from '@capacitor/core';

@Component({
  selector: 'app-kurir',
  templateUrl: './kurir.page.html',
  styleUrls: ['./kurir.page.scss'],
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    IonContent,
    IonItem,
    IonLabel,
    IonInput,
    IonTextarea,
    IonButton,
    IonBadge,
    IonList,
  ],
})
export class KurirPage implements OnInit, OnDestroy {

  // =====================================
  // KONFIGURASI API
  // =====================================

  base = localStorage.getItem('base') || 'http://10.0.2.2:8000/api';
  token = localStorage.getItem('token') || '';

  halaman: string = this.token ? 'paket' : 'login';

  info = '';

  // =====================================
  // DATA LOGIN
  // =====================================

  akun = '';
  password = '';

  // =====================================
  // DATA GANTI PASSWORD
  // =====================================

  lama = '';
  baru = '';
  konfirmasi = '';

  // =====================================
  // DATA EMAIL AKUN
  // =====================================

  email = '';
  emailReset = '';

  // =====================================
  // DATA RESET PASSWORD VIA EMAIL
  // =====================================

  tokenReset = '';
  passwordReset = '';
  konfirmasiReset = '';

  // STATUS TOMBOL RESET PASSWORD
  sedangKirimReset: boolean = false;
  sedangSimpanReset: boolean = false;

  private appUrlListener?: PluginListenerHandle;

  // =====================================
  // DATA PAKET
  // =====================================

  paket: any[] = [];
  dipilih: any = null;
  catatan = '';

  // =====================================
  // DATA COD
  // =====================================

  cod: any = {
    total_belum_disetor: 0,
    paket: [],
  };

  // =====================================
  // INIT DAN DEEP LINK
  // =====================================

  async ngOnInit() {

    try {

      // Menerima link ketika aplikasi sedang terbuka
      this.appUrlListener = await App.addListener(
        'appUrlOpen',
        (event) => {
          this.prosesLinkReset(event.url);
        }
      );

      // Menerima link ketika aplikasi dibuka dari kondisi tertutup
      const launch = await App.getLaunchUrl();

      if (launch?.url) {
        this.prosesLinkReset(launch.url);
      }

    } catch (error) {

      console.error('Gagal memasang listener Deep Link:', error);

    }

    if (this.token && this.halaman !== 'reset-password') {
      this.muatPaket();
    }
  }

  // =====================================
  // MEMPROSES LINK RESET PASSWORD
  // =====================================

  prosesLinkReset(url: string) {

    try {

      const parsed = new URL(url);

      if (
        parsed.protocol !== 'priangankilat:' ||
        parsed.hostname !== 'reset-password'
      ) {
        return;
      }

      this.tokenReset = parsed.searchParams.get('token') || '';
      this.emailReset = parsed.searchParams.get('email') || '';

      if (!this.tokenReset || !this.emailReset) {

        this.info = 'Tautan reset tidak valid. Silakan minta tautan baru.';
        this.halaman = 'reset-password';

        return;
      }

      this.passwordReset = '';
      this.konfirmasiReset = '';
      this.info = '';

      this.halaman = 'reset-password';

      console.log('Deep Link reset password berhasil diterima.');
      console.log('Email reset:', this.emailReset);

    } catch (error) {

      console.error('Gagal membaca link reset:', error);

      this.info = 'Tautan reset tidak valid.';

    }
  }

  // =====================================
  // HAPUS LISTENER
  // =====================================

  ngOnDestroy() {

    this.appUrlListener?.remove();

  }

  // =====================================
  // KONEKSI API
  // =====================================

  async api(
    path: string,
    method = 'GET',
    body: any = null,
    gunakanToken = true
  ) {

    const headers: any = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    };

    // Token hanya dikirim jika memang diperlukan
    if (gunakanToken && this.token) {
      headers.Authorization = 'Bearer ' + this.token;
    }

    let res: Response;

    try {

      res = await fetch(this.base + path, {
        method,
        headers,
        body: body ? JSON.stringify(body) : null,
      });

    } catch (error) {

      console.error('Koneksi API gagal:', error);

      this.info = 'Tidak dapat terhubung ke server. Periksa alamat server API.';

      return null;
    }

    const data = await res.json().catch(() => ({}));

    console.log('API:', path);
    console.log('Status:', res.status);
    console.log('Respons:', data);

    if (res.status === 401 && gunakanToken && this.token) {

      this.keluarLokal();

      this.info = 'Sesi berakhir, silakan masuk lagi.';

      return null;
    }

    if (res.status === 403 && data.code === 'must_change_password') {

      this.halaman = 'ganti';

      this.info = data.message;

      return null;
    }

    if (!res.ok) {

      this.info = data.message || 'Terjadi kesalahan pada server.';

      return null;
    }

    return data;
  }

  // =====================================
  // LOGOUT LOKAL
  // =====================================

  keluarLokal() {

    this.token = '';

    localStorage.removeItem('token');

    this.halaman = 'login';

  }

  // =====================================
  // PINDAH HALAMAN
  // =====================================

  pindah(h: string) {

    this.info = '';

    this.halaman = h;

    if (h === 'paket') {
      this.muatPaket();
    }

    if (h === 'cod') {
      this.muatCod();
    }

    if (h === 'akun') {
      this.muatAkun();
    }
  }

  // =====================================
  // LOGIN KURIR
  // =====================================

  async masuk() {

    this.info = '';

    if (!this.akun.trim() || !this.password) {

      this.info = 'Kode kurir dan password wajib diisi.';

      return;
    }

    localStorage.setItem('base', this.base);

    const d = await this.api('/login', 'POST', {
      akun: this.akun.trim(),
      password: this.password,
    }, false);

    if (!d) return;

    this.token = d.token;

    localStorage.setItem('token', d.token);

    this.password = '';

    if (d.must_change_password) {

      this.halaman = 'ganti';

      this.info = 'Silakan ganti password terlebih dahulu.';

    } else {

      this.pindah('paket');

    }
  }

  // =====================================
  // LUPA PASSWORD
  // =====================================

  async kirimResetPassword() {

    if (this.sedangKirimReset) return;

    this.info = '';

    if (!this.akun.trim()) {

      this.info = 'Kode kurir wajib diisi.';

      return;
    }

    if (!this.emailReset.trim()) {

      this.info = 'Email wajib diisi.';

      return;
    }

    const email = this.emailReset.trim();

    this.sedangKirimReset = true;

    try {

      console.log('Mengirim permintaan reset password...');
      console.log('Alamat API:', this.base + '/password/forgot');
      console.log('Kode kurir:', this.akun.trim());
      console.log('Email:', email);

      const d = await this.api('/password/forgot', 'POST', {
        akun: this.akun.trim(),
        email: email,
      }, false);

      console.log('Hasil reset password:', d);

      if (!d) return;

      // Pop-up konfirmasi berhasil
      alert(
        'Tautan reset password berhasil dikirim!\n\nSilakan cek email kamu melalui Mailpit.'
      );

    } catch (error) {

      console.error('Gagal mengirim tautan reset:', error);

      this.info = 'Gagal mengirim tautan reset. Silakan coba lagi.';

    } finally {

      this.sedangKirimReset = false;

    }
  }

  // =====================================
  // SIMPAN PASSWORD BARU DARI EMAIL
  // =====================================

  async simpanResetPassword() {

    if (this.sedangSimpanReset) return;

    this.info = '';

    if (!this.tokenReset || !this.emailReset) {

      this.info = 'Tautan reset tidak valid. Silakan minta tautan baru.';

      return;
    }

    if (!this.passwordReset || !this.konfirmasiReset) {

      this.info = 'Semua kolom password wajib diisi.';

      return;
    }

    if (this.passwordReset.length < 8) {

      this.info = 'Password baru minimal 8 karakter.';

      return;
    }

    if (this.passwordReset !== this.konfirmasiReset) {

      this.info = 'Konfirmasi password tidak sama.';

      return;
    }

    this.sedangSimpanReset = true;

    try {

      const d = await this.api('/password/reset', 'POST', {

        email: this.emailReset,

        token: this.tokenReset,

        password: this.passwordReset,

        password_confirmation: this.konfirmasiReset,

      }, false);

      if (!d) return;

      // Bersihkan token login lama
      this.token = '';

      localStorage.removeItem('token');

      // Bersihkan data reset password
      this.tokenReset = '';
      this.passwordReset = '';
      this.konfirmasiReset = '';
      this.emailReset = '';

      // Kembali ke halaman login
      this.halaman = 'login';

      // Pop-up berhasil reset
      alert(
        d.message || 'Password berhasil direset. Silakan login kembali.'
      );

    } catch (error) {

      console.error('Gagal menyimpan password baru:', error);

      this.info = 'Gagal menyimpan password baru. Silakan coba lagi.';

    } finally {

      this.sedangSimpanReset = false;

    }
  }

  // =====================================
  // GANTI PASSWORD
  // =====================================

  async ganti() {

    this.info = '';

    if (!this.lama || !this.baru || !this.konfirmasi) {

      this.info = 'Semua kolom password wajib diisi.';

      return;
    }

    if (this.baru.length < 8) {

      this.info = 'Password baru minimal 8 karakter.';

      return;
    }

    if (this.baru !== this.konfirmasi) {

      this.info = 'Konfirmasi password tidak sama.';

      return;
    }

    const d = await this.api('/password', 'POST', {

      password_lama: this.lama,

      password_baru: this.baru,

      password_baru_confirmation: this.konfirmasi,

    });

    if (!d) return;

    this.lama = '';
    this.baru = '';
    this.konfirmasi = '';

    this.pindah('paket');

    this.info = d.message || 'Password berhasil diganti.';
  }

  // =====================================
  // PAKET
  // =====================================

  async muatPaket() {

    const d = await this.api('/paket');

    if (d) {
      this.paket = d;
    }
  }

  buka(p: any) {

    this.dipilih = p;

    this.catatan = '';

    this.info = '';

    this.halaman = 'detail';
  }

  async kirim(hasil: string) {

    this.info = '';

    if (hasil === 'Gagal Kirim' && !this.catatan.trim()) {

      this.info = 'Catatan wajib diisi untuk Gagal Kirim.';

      return;
    }

    const d = await this.api('/paket/' + this.dipilih.id + '/hasil', 'POST', {

      hasil,

      catatan: this.catatan,

    });

    if (!d) return;

    this.pindah('paket');

    this.info = d.message;
  }

  // =====================================
  // COD
  // =====================================

  async muatCod() {

    const d = await this.api('/cod');

    if (d) {
      this.cod = d;
    }
  }

  // =====================================
  // EMAIL AKUN
  // =====================================

  async muatAkun() {

    const d = await this.api('/akun');

    if (d) {
      this.email = d.email || '';
    }
  }

  async simpanEmail() {

    this.info = '';

    const d = await this.api('/akun/email', 'PUT', {

      email: this.email.trim() || null,

    });

    if (!d) return;

    this.email = d.email || '';

    this.info = d.message || 'Email berhasil disimpan.';
  }

  // =====================================
  // LOGOUT
  // =====================================

  async keluar() {

    await this.api('/logout', 'POST');

    this.keluarLokal();

    this.info = 'Anda berhasil keluar.';
  }

}

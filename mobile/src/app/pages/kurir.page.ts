import { Component, OnInit, OnDestroy, NgZone, ChangeDetectorRef, inject } from '@angular/core';
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
  IonList,
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

  base = 'http://192.168.1.8:8000/api';
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
  private lastProcessedUrl = '';
  private zone = inject(NgZone);
  private cdr = inject(ChangeDetectorRef);

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
      // Pastikan tidak ada listener ganda
      if (this.appUrlListener) {
        await this.appUrlListener.remove();
        this.appUrlListener = undefined;
      }

      // 1. Menerima deep link ketika aplikasi sedang berjalan (background/foreground)
      this.appUrlListener = await App.addListener('appUrlOpen', (event) => {
        if (!event?.url) return;

        this.zone.run(() => {
          console.log('Deep link diterima (appUrlOpen):', event.url);
          this.prosesLinkReset(event.url);
        });
      });

      // 2. Menerima deep link ketika aplikasi dibuka dari kondisi tertutup (cold start)
      const launch = await App.getLaunchUrl();

      if (launch?.url) {
        this.zone.run(() => {
          console.log('Launch URL diterima (getLaunchUrl):', launch.url);
          this.prosesLinkReset(launch.url);
        });
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
    if (!url) return;

    // Cegah pemrosesan deep link yang sama secara berulang
    if (this.lastProcessedUrl === url && this.halaman === 'reset-password') {
      return;
    }

    try {
      const parsed = new URL(url);

      // Validasi scheme priangankilat://
      if (parsed.protocol !== 'priangankilat:') {
        return;
      }

      // Tangani priangankilat://reset-password?... maupun priangankilat:///reset-password?...
      const host = parsed.hostname.toLowerCase();
      const path = parsed.pathname.replace(/^\/+/, '').toLowerCase();
      const isResetRoute = host === 'reset-password' || (!host && path === 'reset-password');

      if (!isResetRoute) {
        return;
      }

      const tokenParam = (parsed.searchParams.get('token') || '').trim();
      const emailParam = (parsed.searchParams.get('email') || '').trim();

      this.lastProcessedUrl = url;
      this.passwordReset = '';
      this.konfirmasiReset = '';

      if (!tokenParam || !emailParam) {
        this.tokenReset = '';
        this.emailReset = emailParam;
        this.info = 'Tautan reset tidak valid atau parameter tidak lengkap. Silakan minta tautan baru.';
        this.halaman = 'reset-password';
        this.cdr.detectChanges();
        return;
      }

      // Isi variabel reset password dan langsung tampilkan halaman reset-password
      this.tokenReset = tokenParam;
      this.emailReset = emailParam;
      this.info = '';
      this.halaman = 'reset-password';

      // Paksa Angular/Ionic memperbarui tampilan secara langsung tanpa interaksi textfield
      this.cdr.detectChanges();

      console.log('Deep Link reset password berhasil diproses.');
      console.log('Email reset:', this.emailReset);
    } catch (error) {
      console.error('Gagal membaca link reset:', error);
      this.info = 'Tautan reset tidak valid.';
      this.cdr.detectChanges();
    }
  }

  // =====================================
  // HAPUS LISTENER
  // =====================================

  ngOnDestroy() {
    if (this.appUrlListener) {
      this.appUrlListener.remove();
      this.appUrlListener = undefined;
    }
  }

  // =====================================
  // KONEKSI API
  // =====================================

  async api(path: string, method = 'GET', body: any = null, gunakanToken = true) {
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

      this.zone.run(() => {
        this.info = 'Tidak dapat terhubung ke server. Periksa alamat server API.';
        this.cdr.detectChanges();
      });

      return null;
    }

    const data = await res.json().catch(() => ({}));

    console.log('API:', path);
    console.log('Status:', res.status);
    console.log('Respons:', data);

    if (res.status === 401 && gunakanToken && this.token) {
      this.zone.run(() => {
        this.keluarLokal();
        this.info = 'Sesi berakhir, silakan masuk lagi.';
        this.cdr.detectChanges();
      });

      return null;
    }

    if (res.status === 403 && data.code === 'must_change_password') {
      this.zone.run(() => {
        this.halaman = 'ganti';
        this.info = data.message;
        this.cdr.detectChanges();
      });

      return null;
    }

    if (!res.ok) {
      this.zone.run(() => {
        this.info = data.message || 'Terjadi kesalahan pada server.';
        this.cdr.detectChanges();
      });

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

    this.cdr.detectChanges();
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

    const d = await this.api(
      '/login',
      'POST',
      {
        akun: this.akun.trim(),
        password: this.password,
      },
      false,
    );

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
    this.cdr.detectChanges();

    try {
      console.log('Mengirim permintaan reset password...');
      console.log('Alamat API:', this.base + '/password/forgot');
      console.log('Kode kurir:', this.akun.trim());
      console.log('Email:', email);

      const d = await this.api(
        '/password/forgot',
        'POST',
        {
          akun: this.akun.trim(),
          email: email,
        },
        false,
      );

      console.log('Hasil reset password:', d);

      if (!d) return;

      // Reset cache URL terakhir agar tautan baru dari email berikutnya tetap diproses
      this.lastProcessedUrl = '';

      setTimeout(() => {
        alert('Tautan reset password berhasil dikirim!\n\nSilakan cek email kamu melalui Mailpit.');
      }, 100);
    } catch (error) {
      console.error('Gagal mengirim tautan reset:', error);

      this.zone.run(() => {
        this.info = 'Gagal mengirim tautan reset. Silakan coba lagi.';
      });
    } finally {
      this.zone.run(() => {
        this.sedangKirimReset = false;
        this.cdr.detectChanges();
      });
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
      this.cdr.detectChanges();
      return;
    }

    if (!this.passwordReset || !this.konfirmasiReset) {
      this.info = 'Semua kolom password wajib diisi.';
      this.cdr.detectChanges();
      return;
    }

    if (this.passwordReset.length < 8) {
      this.info = 'Password baru minimal 8 karakter.';
      this.cdr.detectChanges();
      return;
    }

    if (this.passwordReset !== this.konfirmasiReset) {
      this.info = 'Konfirmasi password tidak sama.';
      this.cdr.detectChanges();
      return;
    }

    this.sedangSimpanReset = true;
    this.cdr.detectChanges();

    try {
      const d = await this.api(
        '/password/reset',
        'POST',
        {
          email: this.emailReset,
          token: this.tokenReset,
          password: this.passwordReset,
          password_confirmation: this.konfirmasiReset,
        },
        false,
      );

      console.log('HASIL RESET:', d);

      if (!d) return;

      const pesanSukses = d.message || 'Password berhasil direset. Silakan login kembali.';

      // Perbarui state di dalam NgZone dan render halaman login terlebih dahulu sebelum alert()
      this.zone.run(() => {
        // Bersihkan token login lama
        this.token = '';
        localStorage.removeItem('token');

        // Bersihkan data reset password
        this.tokenReset = '';
        this.passwordReset = '';
        this.konfirmasiReset = '';
        this.emailReset = '';
        this.lastProcessedUrl = '';

        // Ubah halaman ke login dan tampilkan pesan sukses
        this.halaman = 'login';
        this.info = pesanSukses;
        this.sedangSimpanReset = false;

        // Paksa Angular/Ionic memperbarui tampilan ke halaman login seketika
        this.cdr.detectChanges();
      });

      // Tunda alert agar tidak memblokir proses render perpindahan halaman ke 'login'
      setTimeout(() => {
        alert(pesanSukses);
      }, 150);
    } catch (error) {
      console.error('Gagal menyimpan password baru:', error);

      this.zone.run(() => {
        this.info = 'Gagal menyimpan password baru. Silakan coba lagi.';
      });
    } finally {
      this.zone.run(() => {
        this.sedangSimpanReset = false;
        this.cdr.detectChanges();
      });
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
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { 
  IonContent,
  IonItem, IonLabel, IonInput, IonTextarea, IonButton, 
  IonBadge, IonList, 
} from '@ionic/angular';

@Component({
  selector: 'app-kurir',
  templateUrl: './kurir.page.html',
  styleUrls: ['./kurir.page.scss'],
  standalone: true,
  imports: [
    CommonModule, FormsModule, IonContent, IonItem, IonLabel, IonInput, 
    IonTextarea, IonButton, IonBadge, IonList
  ]
})
export class KurirPage implements OnInit {
  base = localStorage.getItem('base') || 'http://10.0.2.2:8000/api';
  token = localStorage.getItem('token') || '';
  halaman = this.token ? 'paket' : 'login';
  info = '';
  akun = '';
  password = '';
  lama = '';
  baru = '';
  konfirmasi = '';
  paket: any[] = [];
  dipilih: any = null;
  catatan = '';
  cod: any = { total_belum_disetor: 0, paket: [] };

  ngOnInit() {
    if (this.token) this.muatPaket();
  }

  async api(path: string, method = 'GET', body: any = null) {
    const headers: any = { Accept: 'application/json', 'Content-Type': 'application/json' };
    if (this.token) headers.Authorization = 'Bearer ' + this.token;
    let res;
    try {
      res = await fetch(this.base + path, { method, headers, body: body ? JSON.stringify(body) : null });
    } catch (e) {
      this.info = 'Tidak dapat terhubung ke server.';
      return null;
    }
    const data = await res.json().catch(() => ({}));
    if (res.status === 401 && this.token) {
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
      this.info = data.message || 'Terjadi kesalahan.';
      return null;
    }
    return data;
  }

  keluarLokal() {
    this.token = '';
    localStorage.removeItem('token');
    this.halaman = 'login';
  }

  pindah(h: string) {
    this.info = '';
    this.halaman = h;
    if (h === 'paket') this.muatPaket();
    if (h === 'cod') this.muatCod();
  }

  async masuk() {
    this.info = '';
    localStorage.setItem('base', this.base);
    const d = await this.api('/login', 'POST', { akun: this.akun, password: this.password });
    if (!d) return;
    this.token = d.token;
    localStorage.setItem('token', d.token);
    this.password = '';
    if (d.must_change_password) this.halaman = 'ganti';
    else this.pindah('paket');
  }

  async ganti() {
  console.log('Password lama terisi:', !!this.lama);
  console.log('Password baru terisi:', !!this.baru);
  console.log('Konfirmasi terisi:', !!this.konfirmasi);

  const d = await this.api('/password', 'POST', {
    password_lama: this.lama,
    password_baru: this.baru,
    password_baru_confirmation: this.konfirmasi,
  });

  if (!d) return;

  this.lama = this.baru = this.konfirmasi = '';
  this.pindah('paket');
  this.info = 'Password berhasil diganti.';
}
  async muatPaket() {
    const d = await this.api('/paket');
    if (d) this.paket = d;
  }

  buka(p: any) {
    this.dipilih = p;
    this.catatan = '';
    this.info = '';
    this.halaman = 'detail';
  }

  async kirim(hasil: string) {
    if (hasil === 'Gagal Kirim' && !this.catatan.trim()) {
      this.info = 'Catatan wajib diisi untuk Gagal Kirim.';
      return;
    }
    const d = await this.api('/paket/' + this.dipilih.id + '/hasil', 'POST', { hasil, catatan: this.catatan });
    if (!d) return;
    this.pindah('paket');
    this.info = d.message;
  }

  async muatCod() {
    const d = await this.api('/cod');
    if (d) this.cod = d;
  }

  async keluar() {
    await this.api('/logout', 'POST');
    this.keluarLokal();
  }
}
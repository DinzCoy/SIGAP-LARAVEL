<?php

use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Role;
use App\Models\User;

function buatAdminFaq(): User
{
    $user = User::factory()->create();
    $role = Role::firstOrCreate(['id' => User::ROLE_ADMIN], ['name' => 'Administrator']);
    $user->roles()->attach($role->id);
    return $user;
}

function buatUserFaq(): User
{
    $user = User::factory()->create();
    $role = Role::firstOrCreate(['id' => User::ROLE_USER], ['name' => 'User']);
    $user->roles()->attach($role->id);
    return $user;
}

function buatKategori(): FaqCategory
{
    $uid = uniqid();
    return FaqCategory::create([
        'name' => 'Kategori Test ' . $uid,
        'slug' => 'kategori-test-' . $uid,
    ]);
}

function buatArtikelFaq(int $categoryId, bool $published = true): Faq
{
    return Faq::create([
        'faq_category_id' => $categoryId,
        'question'        => 'Pertanyaan test ' . uniqid() . '?',
        'answer'          => 'Jawaban test yang cukup panjang untuk memenuhi kebutuhan konten artikel FAQ ini.',
        'is_published'    => $published,
    ]);
}

test('user authenticated dapat mengakses halaman FAQ publik', function () {
    $user = buatUserFaq();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get('/faq');

    $response->assertStatus(200);
    $response->assertViewIs('faq.index');
});

test('guest tidak dapat mengakses halaman FAQ (harus login)', function () {
    $response = $this->get('/faq');
    $response->assertRedirect('/login');
});

test('user dapat melihat artikel FAQ yang dipublikasi', function () {
    $user = buatUserFaq();
    $kategori = buatKategori();
    $artikel = buatArtikelFaq($kategori->id, true);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get("/faq/{$artikel->id}");

    $response->assertStatus(200);
    $response->assertViewIs('faq.show');
});

test('artikel FAQ yang belum dipublikasi mengembalikan 404', function () {
    $user = buatUserFaq();
    $kategori = buatKategori();
    $artikelDraft = buatArtikelFaq($kategori->id, false);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get("/faq/{$artikelDraft->id}");

    $response->assertStatus(404);
});

test('melihat artikel FAQ menambah counter views', function () {
    $user = buatUserFaq();
    $kategori = buatKategori();
    $artikel = buatArtikelFaq($kategori->id, true);
    $viewsAwal = $artikel->views;

    $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get("/faq/{$artikel->id}");

    $artikel->refresh();
    expect($artikel->views)->toBe($viewsAwal + 1);
});

test('user dapat memberi feedback helpful pada artikel', function () {
    $user = buatUserFaq();
    $kategori = buatKategori();
    $artikel = buatArtikelFaq($kategori->id, true);
    $helpfulAwal = $artikel->helpful_count;

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->post("/faq/{$artikel->id}/feedback", [
            'type' => 'helpful',
        ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $artikel->refresh();
    expect($artikel->helpful_count)->toBe($helpfulAwal + 1);
});

test('user dapat memberi feedback unhelpful pada artikel', function () {
    $user = buatUserFaq();
    $kategori = buatKategori();
    $artikel = buatArtikelFaq($kategori->id, true);
    $unhelpfulAwal = $artikel->unhelpful_count;

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->post("/faq/{$artikel->id}/feedback", [
            'type' => 'unhelpful',
        ]);

    $response->assertStatus(200);

    $artikel->refresh();
    expect($artikel->unhelpful_count)->toBe($unhelpfulAwal + 1);
});

test('validasi menolak tipe feedback yang tidak valid', function () {
    $user = buatUserFaq();
    $kategori = buatKategori();
    $artikel = buatArtikelFaq($kategori->id, true);

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->withHeaders(['Accept' => 'application/json'])
        ->post("/faq/{$artikel->id}/feedback", [
            'type' => 'netral',
        ]);

    $response->assertStatus(422);
});

test('admin dapat melihat daftar kategori FAQ', function () {
    $admin = buatAdminFaq();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get('/admin/faq/categories');

    $response->assertStatus(200);
});

test('admin dapat membuat kategori FAQ baru', function () {
    $admin = buatAdminFaq();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/admin/faq/categories', [
            'name' => 'Kategori Baru ' . uniqid(),
        ]);

    $response->assertRedirect();
});

test('admin dapat menghapus kategori FAQ', function () {
    $admin = buatAdminFaq();
    $kategori = buatKategori();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->delete("/admin/faq/categories/{$kategori->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('faq_categories', ['id' => $kategori->id]);
});

test('non-admin diblokir dari manajemen kategori FAQ', function () {
    $user = buatUserFaq();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->get('/admin/faq/categories');

    $response->assertStatus(403);
});

test('admin dapat melihat daftar artikel FAQ', function () {
    $admin = buatAdminFaq();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get('/admin/faq/articles');

    $response->assertStatus(200);
});

test('admin dapat membuat artikel FAQ baru', function () {
    $admin = buatAdminFaq();
    $kategori = buatKategori();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/admin/faq/articles', [
            'faq_category_id' => $kategori->id,
            'question'        => 'Bagaimana cara reset password?',
            'answer'          => 'Untuk reset password, silakan hubungi administrator dan berikan bukti identitas Anda.',
            'is_published'    => true,
        ]);

    $response->assertRedirect(route('admin.faq.articles.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('faqs', [
        'question'     => 'Bagaimana cara reset password?',
        'is_published' => 1,
    ]);
});

test('validasi menolak artikel FAQ tanpa pertanyaan', function () {
    $admin = buatAdminFaq();
    $kategori = buatKategori();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->post('/admin/faq/articles', [
            'faq_category_id' => $kategori->id,
            'answer'          => 'Jawaban ada tapi pertanyaan tidak ada',
        ]);

    $response->assertSessionHasErrors(['question']);
});

test('admin dapat mengedit artikel FAQ', function () {
    $admin = buatAdminFaq();
    $kategori = buatKategori();
    $artikel = buatArtikelFaq($kategori->id);

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->put("/admin/faq/articles/{$artikel->id}", [
            'faq_category_id' => $kategori->id,
            'question'        => 'Pertanyaan yang sudah diedit?',
            'answer'          => 'Jawaban yang sudah diedit dengan konten yang lebih lengkap.',
            'is_published'    => true,
        ]);

    $response->assertRedirect(route('admin.faq.articles.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('faqs', [
        'id'       => $artikel->id,
        'question' => 'Pertanyaan yang sudah diedit?',
    ]);
});

test('admin dapat menghapus artikel FAQ', function () {
    $admin = buatAdminFaq();
    $kategori = buatKategori();
    $artikel = buatArtikelFaq($kategori->id);

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->delete("/admin/faq/articles/{$artikel->id}");

    $response->assertRedirect(route('admin.faq.articles.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseMissing('faqs', ['id' => $artikel->id]);
});

test('non-admin diblokir dari manajemen artikel FAQ', function () {
    $user = buatUserFaq();

    $response = $this->actingAs($user)
        ->withSession(['active_role_id' => User::ROLE_USER])
        ->post('/admin/faq/articles', [
            'question' => 'Pertanyaan hacker',
            'answer'   => 'Jawaban hacker',
        ]);

    $response->assertStatus(403);
});

test('admin dapat mengakses form create artikel FAQ', function () {
    $admin = buatAdminFaq();

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get('/admin/faq/articles/create');

    $response->assertStatus(200);
});

test('admin dapat mengakses form edit artikel FAQ', function () {
    $admin = buatAdminFaq();
    $kategori = buatKategori();
    $artikel = buatArtikelFaq($kategori->id);

    $response = $this->actingAs($admin)
        ->withSession(['active_role_id' => User::ROLE_ADMIN])
        ->get("/admin/faq/articles/{$artikel->id}/edit");

    $response->assertStatus(200);
});

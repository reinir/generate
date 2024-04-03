Contoh pemakaian
================

Generate 1 form
---------------

```bash
php generate.php contoh.yaml
```

Sesuaikan `output` di file `.yaml`.

Contoh definisi form:

```yaml
output: contoh.html #output wajib ada
---
Diabetes Melitus:
  Gula Darah Sewaktu:
    field: gula_darah_sewaktu
    type: checkbox
  Gula Darah 2JPP:
    field: gula_darah_2jpp
    type: checkbox

Kirim:
  type: button
  class: primary
```

Generate 1 project
------------------

```bash
php generate.php simrs.yaml
```

Sesuaikan `definitions` di file `.yaml`

Contoh definisi project:

```yaml
project: #project wajib ada
  name: SIMRS
forms:
  - simrs/contoh.yaml
```

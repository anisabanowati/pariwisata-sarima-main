import pandas as pd
import matplotlib.pyplot as plt
from IPython.display import display
import ipywidgets as widgets
from datetime import datetime

# Dataset kosong
data = []

# Widget input
nama_wisata = widgets.Text(description='Nama Wisata:')
jumlah_pengunjung = widgets.IntText(description='Jumlah Pengunjung:')
pendapatan = widgets.FloatText(description='Pendapatan (Rp):')
sewa_gedung = widgets.FloatText(description='Sewa Gedung (Rp):')
rentang_waktu = widgets.Dropdown(options=['Harian', 'Mingguan', 'Bulanan'], description='Rentang Waktu:')
tanggal = widgets.DatePicker(description='Tanggal:')
submit_button = widgets.Button(description='Simpan', button_style='success')

output = widgets.Output()

def on_submit_clicked(b):
    with output:
        data.append({
            'Nama Wisata': nama_wisata.value,
            'Jumlah Pengunjung': jumlah_pengunjung.value,
            'Pendapatan (Rp)': pendapatan.value,
            'Sewa Gedung (Rp)': sewa_gedung.value,
            'Rentang Waktu': rentang_waktu.value,
            'Tanggal': tanggal.value.strftime('%Y-%m-%d') if tanggal.value else None
        })

        df = pd.DataFrame(data)
        output.clear_output()
        display(df)

        # Plot pendapatan dan pengunjung
        if len(df) > 0:
            fig, ax1 = plt.subplots(figsize=(10,5))

            ax2 = ax1.twinx()
            df['Tanggal'] = pd.to_datetime(df['Tanggal'])

            df_sorted = df.sort_values('Tanggal')

            ax1.bar(df_sorted['Tanggal'], df_sorted['Pendapatan (Rp)'], color='skyblue', label='Pendapatan')
            ax2.plot(df_sorted['Tanggal'], df_sorted['Jumlah Pengunjung'], color='green', marker='o', label='Pengunjung')

            ax1.set_xlabel('Tanggal')
            ax1.set_ylabel('Pendapatan (Rp)', color='blue')
            ax2.set_ylabel('Jumlah Pengunjung', color='green')
            ax1.set_title('Grafik Pendapatan dan Jumlah Pengunjung')

            fig.tight_layout()
            plt.show()

submit_button.on_click(on_submit_clicked)

# Tampilkan form input
form = widgets.VBox([
    nama_wisata,
    jumlah_pengunjung,
    pendapatan,
    sewa_gedung,
    rentang_waktu,
    tanggal,
    submit_button,
    output
])

display(form)

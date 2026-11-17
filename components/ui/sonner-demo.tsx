"use client"

import React from "react"
import { toast } from "sonner"
import { Toaster } from "@/components/ui/sonner"

export function SonnerDemo() {
  return (
    <div className="flex flex-wrap gap-2 p-4">
      <Toaster position="bottom-right" richColors />
      <button
        onClick={() =>
          toast("Pembaruan Sistem SkinCheck", {
            description: "Data konfigurasi AI dan token berhasil disinkronkan.",
            action: {
              label: "Undo",
              onClick: () => console.log("Undo action clicked"),
            },
          })
        }
        className="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-medium hover:bg-slate-800 transition"
      >
        Default Toast
      </button>

      <button
        onClick={() =>
          toast.success("Pengaturan Berhasil Disimpan!", {
            description: "Model aktif AI telah dialihkan ke Google Gemini.",
          })
        }
        className="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition"
      >
        Success Toast
      </button>

      <button
        onClick={() =>
          toast.error("Gagal Menghubungi API", {
            description: "Periksa kembali API key atau koneksi internet Anda.",
          })
        }
        className="px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-medium hover:bg-rose-700 transition"
      >
        Error Toast
      </button>
    </div>
  )
}

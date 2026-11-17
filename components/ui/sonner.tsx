"use client"

import { useTheme } from "next-themes"
import { Toaster as Sonner } from "sonner"

type ToasterProps = React.ComponentProps<typeof Sonner>

const Toaster = ({ ...props }: ToasterProps) => {
  const { theme = "system" } = useTheme()

  return (
    <Sonner
      theme={theme as ToasterProps["theme"]}
      className="toaster group"
      toastOptions={{
        classNames: {
          toast:
            "group toast group-[.toaster]:bg-white/85 group-[.toaster]:backdrop-blur-xl group-[.toaster]:border-white/90 group-[.toaster]:shadow-2xl group-[.toaster]:text-slate-900 group-[.toaster]:rounded-2xl font-sans border shadow-[0_20px_40px_-10px_rgba(15,23,42,0.18)]",
          description: "group-[.toast]:text-slate-600 text-xs font-medium",
          actionButton:
            "group-[.toast]:bg-teal-700 group-[.toast]:text-white text-xs font-semibold rounded-lg px-3 py-1.5 shadow-sm hover:bg-teal-800 transition",
          cancelButton:
            "group-[.toast]:bg-slate-100/80 group-[.toast]:text-slate-700 text-xs rounded-lg px-3 py-1.5",
        },
      }}
      {...props}
    />
  )
}

export { Toaster }

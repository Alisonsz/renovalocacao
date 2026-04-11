<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'company_name',        'value' => 'Renova Locação',                    'type' => 'text',     'group' => 'general', 'label' => 'Nome da Empresa'],
            ['key' => 'company_tagline',     'value' => 'Equipamentos de Estética para Locação', 'type' => 'text', 'group' => 'general', 'label' => 'Tagline'],
            ['key' => 'company_about',       'value' => 'Somos especialistas em locação de equipamentos de estética de alta performance. Oferecemos as melhores máquinas de depilação a laser, remoção de tatuagem e muito mais para profissionais que buscam excelência.', 'type' => 'textarea', 'group' => 'general', 'label' => 'Sobre a Empresa'],
            ['key' => 'company_logo',        'value' => '',                                  'type' => 'text',     'group' => 'general', 'label' => 'Logo (path)'],
            ['key' => 'hero_title',          'value' => 'Equipamentos Premium para seu Negócio', 'type' => 'text', 'group' => 'general', 'label' => 'Título do Hero'],
            ['key' => 'hero_subtitle',       'value' => 'Alugue as melhores máquinas de estética com suporte especializado e condições exclusivas para o seu negócio crescer.', 'type' => 'textarea', 'group' => 'general', 'label' => 'Subtítulo do Hero'],
            // Contact
            ['key' => 'whatsapp_number',     'value' => '5511999999999',                     'type' => 'phone',    'group' => 'contact', 'label' => 'WhatsApp (com código do país)'],
            ['key' => 'whatsapp_message',    'value' => 'Olá! Tenho interesse em locação de equipamentos.',  'type' => 'text', 'group' => 'contact', 'label' => 'Mensagem padrão WhatsApp'],
            ['key' => 'contact_email',       'value' => 'contato@renovalocacao.com.br',       'type' => 'email',    'group' => 'contact', 'label' => 'E-mail de Contato'],
            ['key' => 'contact_phone',       'value' => '',                                  'type' => 'phone',    'group' => 'contact', 'label' => 'Telefone de Contato'],
            ['key' => 'contact_address',     'value' => '',                                  'type' => 'text',     'group' => 'contact', 'label' => 'Endereço'],
            ['key' => 'instagram_url',       'value' => '',                                  'type' => 'url',      'group' => 'contact', 'label' => 'Instagram'],
            ['key' => 'facebook_url',        'value' => '',                                  'type' => 'url',      'group' => 'contact', 'label' => 'Facebook'],
            // Email
            ['key' => 'booking_notification_email', 'value' => 'contato@renovalocacao.com.br', 'type' => 'email',  'group' => 'email', 'label' => 'E-mail para receber reservas'],
            ['key' => 'email_from_name',     'value' => 'Renova Locação',                    'type' => 'text',     'group' => 'email', 'label' => 'Nome remetente de e-mail'],
            // SEO
            ['key' => 'seo_title',           'value' => 'Renova Locação | Equipamentos de Estética para Locação', 'type' => 'text', 'group' => 'seo', 'label' => 'SEO Title Padrão'],
            ['key' => 'seo_description',     'value' => 'Renova Locação oferece máquinas de depilação a laser, remoção de tatuagem e outros equipamentos de estética premium para locação.', 'type' => 'textarea', 'group' => 'seo', 'label' => 'SEO Description Padrão'],
            ['key' => 'seo_keywords',        'value' => 'locação equipamentos estética, depilação laser, remoção tatuagem, aluguel máquinas estética', 'type' => 'text', 'group' => 'seo', 'label' => 'SEO Keywords Padrão'],
            // Google Merchant
            ['key' => 'gm_store_id',         'value' => '',                                  'type' => 'text',     'group' => 'merchant', 'label' => 'Google Merchant Store ID'],
            ['key' => 'gm_store_name',       'value' => 'Renova Locação',                    'type' => 'text',     'group' => 'merchant', 'label' => 'Google Merchant Store Name'],
            ['key' => 'gm_store_url',        'value' => '',                                  'type' => 'url',      'group' => 'merchant', 'label' => 'URL da Loja (Google Merchant)'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}

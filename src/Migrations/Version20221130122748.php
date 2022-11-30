<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221130122748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'UPDATE hc_aggregated_quap SET questionnaire_id=2 FROM midata_group WHERE hc_aggregated_quap.group_id=midata_group.id AND midata_group.group_type_id=3'
        );
        $this->addSql('ALTER INDEX idx_ca07161cfe54d947 RENAME TO IDX_52A1C3C6FE54D947');
        $this->addSql('ALTER INDEX idx_7ea7f288fe54d947 RENAME TO IDX_632FF14CFE54D947');
        $this->addSql('ALTER INDEX idx_58535ce26c5ad790 RENAME TO IDX_58F6D1B26C5AD790');
        $this->addSql('ALTER INDEX idx_58535ce2fe54d947 RENAME TO IDX_58F6D1B2FE54D947');
        $this->addSql('ALTER INDEX idx_58535ce2c92efb06 RENAME TO IDX_58F6D1B2C92EFB06');
        $this->addSql('ALTER INDEX idx_58535ce2cb29affc RENAME TO IDX_58F6D1B2CB29AFFC');
        $this->addSql('ALTER INDEX idx_58535ce29d1f29af RENAME TO IDX_58F6D1B29D1F29AF');
        $this->addSql('ALTER INDEX idx_58535ce22ad9dab0 RENAME TO IDX_58F6D1B22AD9DAB0');
        $this->addSql('ALTER INDEX idx_58535ce265e1e15d RENAME TO IDX_58F6D1B265E1E15D');
        $this->addSql('ALTER INDEX idx_58535ce2300ac307 RENAME TO IDX_58F6D1B2300AC307');
        $this->addSql('ALTER INDEX idx_58535ce2a5840c59 RENAME TO IDX_58F6D1B2A5840C59');
        $this->addSql('ALTER INDEX idx_7df96e4fe54d947 RENAME TO IDX_F6151E61FE54D947');
        $this->addSql('ALTER INDEX idx_7df96e483f8186e RENAME TO IDX_F6151E6183F8186E');
        $this->addSql('ALTER INDEX idx_7df96e4c92efb06 RENAME TO IDX_F6151E61C92EFB06');
        $this->addSql('ALTER INDEX idx_7df96e4cb29affc RENAME TO IDX_F6151E61CB29AFFC');
        $this->addSql('ALTER INDEX idx_7df96e49d1f29af RENAME TO IDX_F6151E619D1F29AF');
        $this->addSql('ALTER INDEX idx_7df96e465e1e15d RENAME TO IDX_F6151E6165E1E15D');
        $this->addSql('ALTER INDEX idx_7df96e42ad9dab0 RENAME TO IDX_F6151E612AD9DAB0');
        $this->addSql('ALTER INDEX idx_7df96e4300ac307 RENAME TO IDX_F6151E61300AC307');
        $this->addSql('ALTER INDEX idx_7df96e4a5840c59 RENAME TO IDX_F6151E61A5840C59');
        $this->addSql('ALTER INDEX idx_7df96e4ec2beac1 RENAME TO IDX_F6151E61EC2BEAC1');
        $this->addSql('ALTER INDEX idx_503131b9fe54d947 RENAME TO IDX_F5A6A76FFE54D947');
        $this->addSql('ALTER INDEX idx_503131b9b2c0141a RENAME TO IDX_F5A6A76FB2C0141A');
        $this->addSql('ALTER INDEX idx_503131b97778bb66 RENAME TO IDX_F5A6A76F7778BB66');
        $this->addSql('ALTER INDEX idx_503131b97791aa42 RENAME TO IDX_F5A6A76F7791AA42');
        $this->addSql('ALTER INDEX idx_503131b936296fc RENAME TO IDX_F5A6A76F36296FC');
        $this->addSql('ALTER INDEX idx_503131b92512cd92 RENAME TO IDX_F5A6A76F2512CD92');
        $this->addSql('ALTER INDEX idx_503131b9e0aa62ee RENAME TO IDX_F5A6A76FE0AA62EE');
        $this->addSql('ALTER INDEX idx_503131b994b04f74 RENAME TO IDX_F5A6A76F94B04F74');
        $this->addSql('ALTER INDEX idx_503131b9e04373ca RENAME TO IDX_F5A6A76FE04373CA');
        $this->addSql('ALTER INDEX idx_503131b9a1ac8c4c RENAME TO IDX_F5A6A76FA1AC8C4C');
        $this->addSql('ALTER INDEX idx_503131b964142330 RENAME TO IDX_F5A6A76F64142330');
        $this->addSql('ALTER INDEX idx_503131b964fd3214 RENAME TO IDX_F5A6A76F64FD3214');
        $this->addSql('ALTER INDEX idx_503131b9100e0eaa RENAME TO IDX_F5A6A76F100E0EAA');
        $this->addSql('ALTER INDEX idx_503131b9a5840c59 RENAME TO IDX_F5A6A76FA5840C59');
        $this->addSql('ALTER INDEX idx_503131b9ec2beac1 RENAME TO IDX_F5A6A76FEC2BEAC1');
        $this->addSql('ALTER INDEX idx_a8c54ccbfe54d947 RENAME TO IDX_34D1C261FE54D947');
        $this->addSql('ALTER INDEX idx_a8c54ccbc92efb06 RENAME TO IDX_34D1C261C92EFB06');
        $this->addSql('ALTER INDEX idx_a8c54ccbcb29affc RENAME TO IDX_34D1C261CB29AFFC');
        $this->addSql('ALTER INDEX idx_a8c54ccb9d1f29af RENAME TO IDX_34D1C2619D1F29AF');
        $this->addSql('ALTER INDEX idx_a8c54ccb65e1e15d RENAME TO IDX_34D1C26165E1E15D');
        $this->addSql('ALTER INDEX idx_a8c54ccb2ad9dab0 RENAME TO IDX_34D1C2612AD9DAB0');
        $this->addSql('ALTER INDEX idx_a8c54ccb300ac307 RENAME TO IDX_34D1C261300AC307');
        $this->addSql('ALTER INDEX idx_a8c54ccba5840c59 RENAME TO IDX_34D1C261A5840C59');
        $this->addSql('ALTER INDEX idx_a8c54ccbec2beac1 RENAME TO IDX_34D1C261EC2BEAC1');
        $this->addSql('ALTER INDEX idx_6c3255b8fe54d947 RENAME TO IDX_2C278DACFE54D947');
        $this->addSql('ALTER INDEX idx_6c3255b885e16f6b RENAME TO IDX_2C278DAC85E16F6B');
        $this->addSql('ALTER INDEX idx_6c3255b84118d123 RENAME TO IDX_2C278DAC4118D123');
        $this->addSql('ALTER INDEX idx_6c3255b8ea750e8 RENAME TO IDX_2C278DACEA750E8');
        $this->addSql('ALTER INDEX idx_6c3255b8dd30ffd8 RENAME TO IDX_2C278DACDD30FFD8');
        $this->addSql('ALTER INDEX idx_6c3255b8a5840c59 RENAME TO IDX_2C278DACA5840C59');
        $this->addSql('ALTER INDEX idx_6c3255b8638d302 RENAME TO IDX_2C278DAC638D302');
        $this->addSql('ALTER INDEX idx_6c3255b8ec2beac1 RENAME TO IDX_2C278DACEC2BEAC1');
        $this->addSql('ALTER INDEX idx_7241d5e9fe54d947 RENAME TO IDX_4D5275F9FE54D947');
        $this->addSql('ALTER INDEX idx_7241d5e9c92efb06 RENAME TO IDX_4D5275F9C92EFB06');
        $this->addSql('ALTER INDEX idx_7241d5e9cb29affc RENAME TO IDX_4D5275F9CB29AFFC');
        $this->addSql('ALTER INDEX idx_7241d5e99d1f29af RENAME TO IDX_4D5275F99D1F29AF');
        $this->addSql('ALTER INDEX idx_7241d5e9a5840c59 RENAME TO IDX_4D5275F9A5840C59');
        $this->addSql('ALTER INDEX idx_7241d5e9ec2beac1 RENAME TO IDX_4D5275F9EC2BEAC1');
        $this->addSql('ALTER INDEX idx_8dd17f374c8aa1e2 RENAME TO IDX_8D74F2674C8AA1E2');
        $this->addSql('ALTER INDEX idx_8dd17f37c7470a42 RENAME TO IDX_8D74F267C7470A42');
        $this->addSql('ALTER INDEX idx_8dd17f375e237e06 RENAME TO IDX_8D74F2675E237E06');
        $this->addSql('ALTER INDEX idx_8dd17f374709b432 RENAME TO IDX_8D74F2674709B432');
        $this->addSql('ALTER INDEX idx_8c73b2bc6f7a7e00 RENAME TO IDX_B54F4CEB6F7A7E00');
        $this->addSql('ALTER INDEX idx_8c73b2bc56af75a6 RENAME TO IDX_B54F4CEB56AF75A6');
        $this->addSql('ALTER INDEX idx_8c73b2bca393d2fb RENAME TO IDX_B54F4CEBA393D2FB');
        $this->addSql('ALTER INDEX idx_ec87b8e2ce07e8ff RENAME TO IDX_74216D38CE07E8FF');
        $this->addSql('ALTER INDEX idx_ec87b8e2fe54d947 RENAME TO IDX_74216D38FE54D947');
        $this->addSql('ALTER INDEX idx_37df8ddcce07e8ff RENAME TO IDX_CFB1A705CE07E8FF');
        $this->addSql('ALTER INDEX idx_5002cdf61e27f6bf RENAME TO IDX_62A9283D1E27F6BF');
        $this->addSql('ALTER INDEX idx_6e2908ab58f0fd7a RENAME TO IDX_5C82ED6058F0FD7A');
        $this->addSql('ALTER INDEX idx_6e2908abeffec13c RENAME TO IDX_5C82ED60EFFEC13C');
        $this->addSql('ALTER INDEX idx_6e2908ab48c50931 RENAME TO IDX_5C82ED6048C50931');
        $this->addSql('ALTER INDEX idx_cdd2c18798507f8c RENAME TO IDX_46938E6998507F8C');
        $this->addSql('ALTER INDEX uniq_39037de78cde5729 RENAME TO UNIQ_C4BFEA458CDE5729');
        $this->addSql('ALTER INDEX idx_eccd2277217bbb47 RENAME TO IDX_18BE3E8C217BBB47');
        $this->addSql('ALTER INDEX idx_eccd2277f25d6dc4 RENAME TO IDX_18BE3E8CF25D6DC4');
        $this->addSql('ALTER INDEX idx_eccd2277fe54d947 RENAME TO IDX_18BE3E8CFE54D947');
        $this->addSql('ALTER INDEX idx_eccd2277e7927c74 RENAME TO IDX_18BE3E8CE7927C74');
        $this->addSql('ALTER INDEX uniq_fb8ec5ab8a90aba9 RENAME TO UNIQ_F8C2BE708A90ABA9');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER INDEX idx_632ff14cfe54d947 RENAME TO idx_7ea7f288fe54d947');
        $this->addSql('ALTER INDEX idx_58f6d1b2fe54d947 RENAME TO idx_58535ce2fe54d947');
        $this->addSql('ALTER INDEX idx_58f6d1b2c92efb06 RENAME TO idx_58535ce2c92efb06');
        $this->addSql('ALTER INDEX idx_58f6d1b26c5ad790 RENAME TO idx_58535ce26c5ad790');
        $this->addSql('ALTER INDEX idx_58f6d1b22ad9dab0 RENAME TO idx_58535ce22ad9dab0');
        $this->addSql('ALTER INDEX idx_58f6d1b2a5840c59 RENAME TO idx_58535ce2a5840c59');
        $this->addSql('ALTER INDEX idx_58f6d1b2300ac307 RENAME TO idx_58535ce2300ac307');
        $this->addSql('ALTER INDEX idx_58f6d1b265e1e15d RENAME TO idx_58535ce265e1e15d');
        $this->addSql('ALTER INDEX idx_58f6d1b2cb29affc RENAME TO idx_58535ce2cb29affc');
        $this->addSql('ALTER INDEX idx_58f6d1b29d1f29af RENAME TO idx_58535ce29d1f29af');
        $this->addSql('ALTER INDEX idx_f6151e61a5840c59 RENAME TO idx_7df96e4a5840c59');
        $this->addSql('ALTER INDEX idx_f6151e6165e1e15d RENAME TO idx_7df96e465e1e15d');
        $this->addSql('ALTER INDEX idx_f6151e61300ac307 RENAME TO idx_7df96e4300ac307');
        $this->addSql('ALTER INDEX idx_f6151e61c92efb06 RENAME TO idx_7df96e4c92efb06');
        $this->addSql('ALTER INDEX idx_f6151e61fe54d947 RENAME TO idx_7df96e4fe54d947');
        $this->addSql('ALTER INDEX idx_f6151e612ad9dab0 RENAME TO idx_7df96e42ad9dab0');
        $this->addSql('ALTER INDEX idx_f6151e619d1f29af RENAME TO idx_7df96e49d1f29af');
        $this->addSql('ALTER INDEX idx_f6151e61cb29affc RENAME TO idx_7df96e4cb29affc');
        $this->addSql('ALTER INDEX idx_f6151e61ec2beac1 RENAME TO idx_7df96e4ec2beac1');
        $this->addSql('ALTER INDEX idx_f6151e6183f8186e RENAME TO idx_7df96e483f8186e');
        $this->addSql('ALTER INDEX idx_f5a6a76fa1ac8c4c RENAME TO idx_503131b9a1ac8c4c');
        $this->addSql('ALTER INDEX idx_f5a6a76f100e0eaa RENAME TO idx_503131b9100e0eaa');
        $this->addSql('ALTER INDEX idx_f5a6a76f64fd3214 RENAME TO idx_503131b964fd3214');
        $this->addSql('ALTER INDEX idx_f5a6a76fe04373ca RENAME TO idx_503131b9e04373ca');
        $this->addSql('ALTER INDEX idx_f5a6a76f94b04f74 RENAME TO idx_503131b994b04f74');
        $this->addSql('ALTER INDEX idx_f5a6a76fb2c0141a RENAME TO idx_503131b9b2c0141a');
        $this->addSql('ALTER INDEX idx_f5a6a76fa5840c59 RENAME TO idx_503131b9a5840c59');
        $this->addSql('ALTER INDEX idx_f5a6a76f64142330 RENAME TO idx_503131b964142330');
        $this->addSql('ALTER INDEX idx_f5a6a76fe0aa62ee RENAME TO idx_503131b9e0aa62ee');
        $this->addSql('ALTER INDEX idx_f5a6a76ffe54d947 RENAME TO idx_503131b9fe54d947');
        $this->addSql('ALTER INDEX idx_f5a6a76f7778bb66 RENAME TO idx_503131b97778bb66');
        $this->addSql('ALTER INDEX idx_f5a6a76f2512cd92 RENAME TO idx_503131b92512cd92');
        $this->addSql('ALTER INDEX idx_f5a6a76f36296fc RENAME TO idx_503131b936296fc');
        $this->addSql('ALTER INDEX idx_f5a6a76fec2beac1 RENAME TO idx_503131b9ec2beac1');
        $this->addSql('ALTER INDEX idx_f5a6a76f7791aa42 RENAME TO idx_503131b97791aa42');
        $this->addSql('ALTER INDEX idx_8d74f2674709b432 RENAME TO idx_8dd17f374709b432');
        $this->addSql('ALTER INDEX idx_8d74f267c7470a42 RENAME TO idx_8dd17f37c7470a42');
        $this->addSql('ALTER INDEX idx_8d74f2675e237e06 RENAME TO idx_8dd17f375e237e06');
        $this->addSql('ALTER INDEX idx_8d74f2674c8aa1e2 RENAME TO idx_8dd17f374c8aa1e2');
        $this->addSql('ALTER INDEX idx_b54f4ceba393d2fb RENAME TO idx_8c73b2bca393d2fb');
        $this->addSql('ALTER INDEX idx_b54f4ceb56af75a6 RENAME TO idx_8c73b2bc56af75a6');
        $this->addSql('ALTER INDEX idx_b54f4ceb6f7a7e00 RENAME TO idx_8c73b2bc6f7a7e00');
        $this->addSql('ALTER INDEX idx_34d1c2619d1f29af RENAME TO idx_a8c54ccb9d1f29af');
        $this->addSql('ALTER INDEX idx_34d1c261ec2beac1 RENAME TO idx_a8c54ccbec2beac1');
        $this->addSql('ALTER INDEX idx_34d1c261cb29affc RENAME TO idx_a8c54ccbcb29affc');
        $this->addSql('ALTER INDEX idx_34d1c261300ac307 RENAME TO idx_a8c54ccb300ac307');
        $this->addSql('ALTER INDEX idx_34d1c26165e1e15d RENAME TO idx_a8c54ccb65e1e15d');
        $this->addSql('ALTER INDEX idx_34d1c261fe54d947 RENAME TO idx_a8c54ccbfe54d947');
        $this->addSql('ALTER INDEX idx_34d1c2612ad9dab0 RENAME TO idx_a8c54ccb2ad9dab0');
        $this->addSql('ALTER INDEX idx_34d1c261c92efb06 RENAME TO idx_a8c54ccbc92efb06');
        $this->addSql('ALTER INDEX idx_34d1c261a5840c59 RENAME TO idx_a8c54ccba5840c59');
        $this->addSql('ALTER INDEX idx_4d5275f99d1f29af RENAME TO idx_7241d5e99d1f29af');
        $this->addSql('ALTER INDEX idx_4d5275f9cb29affc RENAME TO idx_7241d5e9cb29affc');
        $this->addSql('ALTER INDEX idx_4d5275f9fe54d947 RENAME TO idx_7241d5e9fe54d947');
        $this->addSql('ALTER INDEX idx_4d5275f9ec2beac1 RENAME TO idx_7241d5e9ec2beac1');
        $this->addSql('ALTER INDEX idx_4d5275f9c92efb06 RENAME TO idx_7241d5e9c92efb06');
        $this->addSql('ALTER INDEX idx_4d5275f9a5840c59 RENAME TO idx_7241d5e9a5840c59');
        $this->addSql('ALTER INDEX idx_2c278dacec2beac1 RENAME TO idx_6c3255b8ec2beac1');
        $this->addSql('ALTER INDEX idx_2c278daca5840c59 RENAME TO idx_6c3255b8a5840c59');
        $this->addSql('ALTER INDEX idx_2c278dacfe54d947 RENAME TO idx_6c3255b8fe54d947');
        $this->addSql('ALTER INDEX idx_2c278dac638d302 RENAME TO idx_6c3255b8638d302');
        $this->addSql('ALTER INDEX idx_2c278dac85e16f6b RENAME TO idx_6c3255b885e16f6b');
        $this->addSql('ALTER INDEX idx_2c278dacdd30ffd8 RENAME TO idx_6c3255b8dd30ffd8');
        $this->addSql('ALTER INDEX idx_2c278dacea750e8 RENAME TO idx_6c3255b8ea750e8');
        $this->addSql('ALTER INDEX idx_2c278dac4118d123 RENAME TO idx_6c3255b84118d123');
        $this->addSql('ALTER INDEX uniq_c4bfea458cde5729 RENAME TO uniq_39037de78cde5729');
        $this->addSql('ALTER INDEX idx_46938e6998507f8c RENAME TO idx_cdd2c18798507f8c');
        $this->addSql('ALTER INDEX idx_62a9283d1e27f6bf RENAME TO idx_5002cdf61e27f6bf');
        $this->addSql('ALTER INDEX idx_5c82ed60effec13c RENAME TO idx_6e2908abeffec13c');
        $this->addSql('ALTER INDEX idx_5c82ed6058f0fd7a RENAME TO idx_6e2908ab58f0fd7a');
        $this->addSql('ALTER INDEX idx_5c82ed6048c50931 RENAME TO idx_6e2908ab48c50931');
        $this->addSql('ALTER INDEX idx_cfb1a705ce07e8ff RENAME TO idx_37df8ddcce07e8ff');
        $this->addSql('ALTER INDEX idx_52a1c3c6fe54d947 RENAME TO idx_ca07161cfe54d947');
        $this->addSql('ALTER INDEX idx_74216d38ce07e8ff RENAME TO idx_ec87b8e2ce07e8ff');
        $this->addSql('ALTER INDEX idx_74216d38fe54d947 RENAME TO idx_ec87b8e2fe54d947');
        $this->addSql('ALTER INDEX uniq_f8c2be708a90aba9 RENAME TO uniq_fb8ec5ab8a90aba9');
        $this->addSql('ALTER INDEX idx_18be3e8cf25d6dc4 RENAME TO idx_eccd2277f25d6dc4');
        $this->addSql('ALTER INDEX idx_18be3e8ce7927c74 RENAME TO idx_eccd2277e7927c74');
        $this->addSql('ALTER INDEX idx_18be3e8cfe54d947 RENAME TO idx_eccd2277fe54d947');
        $this->addSql('ALTER INDEX idx_18be3e8c217bbb47 RENAME TO idx_eccd2277217bbb47');
    }
}

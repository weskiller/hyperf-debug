<?php

declare(strict_types=1);

namespace App\Command;

use App\Concrete\System\Command as SystemCommand;
use App\Model\Hero;
use App\Model\Skin;
use App\Repository\Collector\Crawler;
use App\Repository\Collector\Origin\Official;
use App\Repository\Storage\HeroStorageTrait;
use App\Repository\Storage\ResourceProvider;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Database\Model\Collection;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Parallel;
use RuntimeException;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class HeroImage extends HyperfCommand
{
    use HeroStorageTrait;

    #[Inject]
    protected SystemCommand $systemCommand;

    #[Inject]
    protected Crawler $crawler;

    public function __construct()
    {
        parent::__construct('collect:hero-image');
    }

    public function configure() :void
    {
        parent::configure();
        $this->setDescription('保存英雄和皮肤的图片到本地');
        $this->addOption('download',null,InputOption::VALUE_NONE,'本地化');
    }

    /**
     * @throws RuntimeException
     */
    public function handle() :void
    {
        if($this->input->getOption('download')) {
            $this->download();
            $this->output->success('download success');
        }
        $this->output->success('success');
    }


    public function download() :void
    {
        Hero::with('skins')->chunkById(64,function (Collection $collection){
            $parallel =  new Parallel();
            $collection->map(function (Hero $hero) use($parallel) {
                $parallel->add(function () use($hero) {
                    //保存英雄头像
                    ResourceProvider::copy(self::heroAvatarImagePath($hero),$this->crawler->getImage($hero->avatar));
                    //保存英雄大图
                    ResourceProvider::copy(self::heroBigSkinImagePath($hero),$this->crawler->getImage($hero->avatar));

                    /** @var Skin $skin */
                    foreach ($hero->skins as $skin) {
                        $skin->setRelation('hero',$hero);
                        //保存英雄皮肤头像
                        ResourceProvider::copy(self::heroSkinImagePath($skin,'avatar'),$this->crawler->getImage($skin->avatar));
                        //保存英雄皮肤大图
                        ResourceProvider::copy(self::heroSkinImagePath($skin,'image'),$this->crawler->getImage($skin->image));
                    }
                });
            });
            $parallel->wait();
        });
    }

    /**
     * 英雄头像路径
     * @param Hero $hero
     *
     * @return string
     */
    public static function heroAvatarImagePath(Hero $hero) :string
    {
        return  sprintf('%s/%s.%s',self::heroImagePath($hero->name),$hero->name,pathinfo($hero->avatar)['extension']);
    }

    /**
     * 英雄大图路径
     * @param Hero $hero
     *
     * @return string
     */
    public static function heroBigSkinImagePath(Hero $hero) :string
    {
        return  sprintf('%s/%s.bigskin.%s',self::heroImagePath($hero->name),$hero->name,pathinfo($hero->avatar)['extension']);
    }

    /**
     * 皮肤大图路径
     *
     * @param Skin $skin
     * @param string $attribute
     *
     * @return string
     */
    public static function heroSkinImagePath(Skin $skin,string $attribute) :string
    {
        $info = pathinfo($skin->{$attribute});
        return sprintf('%s/%s',self::heroImagePath($skin->hero->name),preg_replace('/^\d+/',$skin->name,$info['basename']));
    }

    public const HeroImagePath = 'hero/images';

    public static function heroImagePath(?string $heroName = null) :string
    {
        return self::getStoragePath(self::HeroImagePath . ($heroName === null ? '' : ('/' . $heroName)));
    }

    /**
     * @throws null
     */
    public static function import() :void
    {
        Official::import(json_decode(file_get_contents(self::getStoragePath('hero/resource.json')),true,512,JSON_BIGINT_AS_STRING|JSON_THROW_ON_ERROR));
    }
}

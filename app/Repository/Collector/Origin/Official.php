<?php


namespace App\Repository\Collector\Origin;


use App\Model\Hero;
use App\Model\HeroRole;
use App\Model\Skin;
use App\Repository\Collector\Crawler;
use Hyperf\Utils\Parallel;

/**
 * Class Official
 * 官方源 爬虫
 * @package App\Repository\Collector\Origin
 */
class Official
{
    //全英雄资料首页
    public const WebSite = 'https://pvp.qq.com/web201605/herolist.shtml';
    //英雄JSON列表
    public const HeroJson = 'https://pvp.qq.com/web201605/js/herolist.json';
    //英雄详情网页
    public const HeroSite = 'https://pvp.qq.com/web201605/herodetail/%s.shtml';
    //英雄皮肤系列图标
    public const HeroSeriesImage = 'https://game.gtimg.cn/images/yxzj/ingame/skin/icon/%s.png';
    //英雄头像
    public const HeroAvatarImage = 'http://game.gtimg.cn/images/yxzj/img201606/heroimg/%s/%s.jpg';
    public const HeroSkinAvatar = 'https://game.gtimg.cn/images/yxzj/img201606/heroimg/%s/%s-smallskin-%s.jpg';
    //public const HeroSkinImage = 'https://game.gtimg.cn/images/yxzj/img201606/heroimg/%s/%s-bigskin-%s.jpg';
    public const HeroSkinImage = 'https://game.gtimg.cn/images/yxzj/img201606/skin/hero-info/%s/%s-bigskin-%s.jpg';

    public const OriginSeriesId = 1;
    public const RelatedSeriesId = 2;
    /** @var int  */
    public const SeriesIncrementNo = 100;

    public const SkinSeries = [
        //系统默认系列
        self::OriginSeriesId => '源生皮肤',
        self::RelatedSeriesId => '伴生皮肤',
        //官方系列
        self::SeriesIncrementNo + 1 => '勇者',
        self::SeriesIncrementNo + 2 => '猴年限定',
        self::SeriesIncrementNo + 3 => '成就限定',
        self::SeriesIncrementNo + 4 => '圣殿限定',
        self::SeriesIncrementNo + 5 => '限定',
        self::SeriesIncrementNo + 6 => '贵族限定',
        self::SeriesIncrementNo + 7 => '会员限定',
        self::SeriesIncrementNo + 8 => '赏金赛限定',
        self::SeriesIncrementNo + 9 => '战队赛限定',
        self::SeriesIncrementNo + 10 => '必胜客专属',
        self::SeriesIncrementNo + 11 => '情人节限定',
        self::SeriesIncrementNo + 12 => '史诗',
        self::SeriesIncrementNo + 13 => 'S3胜利之神',
        self::SeriesIncrementNo + 14 => 'S3胜利之神',
        self::SeriesIncrementNo + 15 => '传说',
        self::SeriesIncrementNo + 16 => '新春专属',
        self::SeriesIncrementNo + 17 => '御龙在天',
        self::SeriesIncrementNo + 18 => '1周年限定',
        self::SeriesIncrementNo + 19 => 'KPL限定',
        self::SeriesIncrementNo + 20 => '鸡年限定',
        self::SeriesIncrementNo + 21 => '情人节限定',
        self::SeriesIncrementNo + 22 => '五五开黑节',
        self::SeriesIncrementNo + 23 => '2周年限定',
        self::SeriesIncrementNo + 24 => '情人节限定',
        self::SeriesIncrementNo + 25 => '狗年限定',
        self::SeriesIncrementNo + 26 => '荣耀典藏',
        self::SeriesIncrementNo + 27 => '沉稳',
        self::SeriesIncrementNo + 28 => '梅西(正版授权)',
        self::SeriesIncrementNo + 29 => '浪一夏限定',
        self::SeriesIncrementNo + 30 => '敏锐',
        self::SeriesIncrementNo + 31 => '掌握',
        self::SeriesIncrementNo + 32 => 'DC(正版授权)',
        self::SeriesIncrementNo + 33 => '圣斗士星矢',
        self::SeriesIncrementNo + 34 => '守护',
        self::SeriesIncrementNo + 35 => '坚毅',
        self::SeriesIncrementNo + 36 => '三周年限定',
        self::SeriesIncrementNo + 37 => '圣诞限定',
        self::SeriesIncrementNo + 38 => 'FMVP',
        self::SeriesIncrementNo + 39 => 'S14隐龙之影',
        self::SeriesIncrementNo + 40 => '站令限定',
        self::SeriesIncrementNo + 41 => '国家宝藏',
        self::SeriesIncrementNo + 42 => '星传说',
        self::SeriesIncrementNo + 43 => '猪年限定',
        self::SeriesIncrementNo + 44 => '情人节限定',
        self::SeriesIncrementNo + 45 => '情人节限定',
        self::SeriesIncrementNo + 46 => 's15隐龙之影',
        self::SeriesIncrementNo + 47 => '信誉专属',
        self::SeriesIncrementNo + 48 => 's16隐龙之影',
        self::SeriesIncrementNo + 49 => '世冠(2019)',
        self::SeriesIncrementNo + 50 => '活动专属',
        self::SeriesIncrementNo + 51 => 'FMVP',
        self::SeriesIncrementNo + 52 => 'S17凤仪之诏',
        self::SeriesIncrementNo + 53 => '4周年限定',
        self::SeriesIncrementNo + 54 => 'S13隐龙之影',
        self::SeriesIncrementNo + 55 => '圣诞限定',
        self::SeriesIncrementNo + 56 => 's5胜利之神',
        self::SeriesIncrementNo + 57 => 's6胜利之神',
        self::SeriesIncrementNo + 58 => 's7胜利之神',
        self::SeriesIncrementNo + 59 => 's8无畏之灵',
        self::SeriesIncrementNo + 60 => 's9无畏之灵',
        self::SeriesIncrementNo + 61 => 's10无畏之灵',
        self::SeriesIncrementNo + 62 => 's11无畏之灵',
        self::SeriesIncrementNo + 63 => 's12无畏之灵',
        self::SeriesIncrementNo + 64 => 'SNK(正版授权)',
        self::SeriesIncrementNo + 65 => '源梦',
        self::SeriesIncrementNo + 66 => 'S18凤仪之诏',
        self::SeriesIncrementNo + 67 => '鼠年限定',
        self::SeriesIncrementNo + 68 => '情人节限定',
        self::SeriesIncrementNo + 69 => '情人节限定',
        self::SeriesIncrementNo + 70 => 's19凤仪之诏',
        self::SeriesIncrementNo + 71 => 'w五五开黑节',
        self::SeriesIncrementNo + 72 => '五虎上将',
        self::SeriesIncrementNo + 73 => '活动专属',
        self::SeriesIncrementNo + 74 => 's20凤仪之诏',
        self::SeriesIncrementNo + 75 => '世冠(2020)',
        self::SeriesIncrementNo + 76 => '王者之证(2020)',
        self::SeriesIncrementNo + 77 => '5周年限定',
        self::SeriesIncrementNo + 78 => '峡谷导闻',
        self::SeriesIncrementNo + 79 => '贵族限定',
        self::SeriesIncrementNo + 80 => 's22不夜长安',
        self::SeriesIncrementNo + 81 => '牛年限定',
        self::SeriesIncrementNo + 82 => '情人节限定',
        self::SeriesIncrementNo + 83 => 's23不夜长安',
        self::SeriesIncrementNo + 84 => '仙剑奇侠限定',
        self::SeriesIncrementNo + 85 => 's24不夜长安',
    ];

    /**
     * 按名称分组的皮肤系列
     */
    public const GroupNameSeriesIncrementNo = 1000;

    protected Crawler $crawler;

    public function __construct()
    {
        $this->crawler = new Crawler();
    }

    /**
     * @param $resource
     * @throws
     */
    public static function import($resource): void
    {
        ['classes'=> $classes,'heroes' => $heroes,'series' => $series] = $resource;
        $roleMap = [];
        foreach ($classes as $class) {
            $roleMap[$class['type']] = HeroRole::firstOrCreate([
                'name' => $class['name'],
            ]);
        }
        $parallel = new Parallel(64);
        foreach ($heroes as $item)  {
            $parallel->add(static function() use($roleMap,$item) {
                    $hero = Hero::create([
                        'id' => $item['no'],
                        'hero_role_id' => $roleMap[$item['type']]->id,
                        'name' => $item['name'],
                        'avatar' => $item['avatar'],
                        'image' => $item['image'],
                        'additional' => [
                            Hero::AdditionalOrigin => [
                                $item,
                            ]
                        ]
                    ]);
                    foreach ($item['skins'] as $index => $skinItem) {
                        /** @var Skin $skin */
                        $skin = $hero->skins()->create([
                            'id' => self::skinId($item['no'],$index),
                            'name' => $skinItem['name'],
                            'avatar' => $skinItem['avatar'],
                            'image' => $skinItem['image'],
                            'price' => 0,
                            'additional' => [
                                Skin::AdditionalOrigin => [
                                    $skinItem,
                                ]
                            ]
                        ]);
                    }
                });
        }
        $parallel->wait();
    }

    /**
     * @param string $no
     *
     * @return string
     */
    public function heroSite(string $no):string
    {
        return sprintf(self::HeroSite,$no);
    }

    /**
     * 通过系列no获取系列id
     * @param int $seriesNo
     * @param int $index
     *
     * @return int
     */
    public static function guessSeriesId(int $seriesNo,int $index):int
    {
        if($seriesNo === 0) {
            return $index === 0 ? self::OriginSeriesId : self::RelatedSeriesId;
        }
        return self::SeriesIncrementNo + $seriesNo;
    }

    /**
     * 通过系列id获取官方系列no
     * @param int $seriesId
     *
     * @return int|null
     */
    public static function guessSeriesNo(int $seriesId): ?int
    {
        if($seriesId < self::SeriesIncrementNo || $seriesId > self::GroupNameSeriesIncrementNo) {
            return null;
        }
        return $seriesId - self::SeriesIncrementNo;
    }

    public const MatchesJson = 'https://itea-cdn.qq.com/file/ingame/smoba/allMatchpage%s.json';

    /**
     * @param $heroNo
     * @param $index
     *
     * @return int
     */
    public static function skinId($heroNo,$index) :int
    {
        return $heroNo . sprintf('%02d',$index + 1);
    }
}
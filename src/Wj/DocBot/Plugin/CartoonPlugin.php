<?php

namespace Wj\DocBot\Plugin;

use Wj\DocBot\SpreadSheetStack;
use Phoebe\Event\Event;
use Phoebe\Plugin\PluginInterface;

class CartoonPlugin implements PluginInterface
{
    const TRIGGER_PATTERN = '/^DocSprintBot:? shows us a cartoon/';

    const MESSAGE = 'Here you go: %s';

    private $cartoons = array(
        "http://www.jeffpalm.com/fox/fox.jpg",
        "http://imgs.xkcd.com/comics/exploits_of_a_mom.png",
        "http://geekfun.pl/pm_build_swing.gif",
        "http://imgs.xkcd.com/comics/sandwich.png",
        "http://i.stack.imgur.com/fvdkb.png",
        "http://blog.pengoworks.com/enclosures/wtfm_cf7237e5-a580-4e22-a42a-f8597dd6c60b.jpg",
        "http://imgs.xkcd.com/comics/random_number.png",
        "http://dilbert.com/dyn/str_strip/000000000/00000000/0000000/000000/00000/1000/100/1125/1125.strip.sunday.gif",
        "http://dilbert.com/dyn/str_strip/000000000/00000000/0000000/000000/20000/1000/100/21168/21168.strip.gif",
        "http://imgs.xkcd.com/comics/compiling.png",
        "http://imgs.xkcd.com/comics/ballmer_peak.png",
        "http://imgs.xkcd.com/comics/goto.png",
        "http://dilbert.com/dyn/str_strip/000000000/00000000/0000000/000000/00000/1000/400/1494/1494.strip.gif",
        "http://imgs.xkcd.com/comics/duty_calls.png",
        "http://imgs.xkcd.com/comics/regular_expressions.png",
        "http://dilbert.com/dyn/str_strip/000000000/00000000/0000000/000000/10000/8000/200/18264/18264.strip.gif",
        "http://dilbert.com/dyn/str_strip/000000000/00000000/0000000/000000/00000/1000/700/1791/1791.strip.gif",
        "http://files.myopera.com/freejerk/files/bug-feature.jpg",
        "http://dilbert.com/dyn/str_strip/000000000/00000000/0000000/000000/00000/2000/300/2318/2318.strip.gif",
        "http://dilbert.com/dyn/str_strip/000000000/00000000/0000000/000000/00000/0000/800/880/880.strip.sunday.gif",
        "http://imgs.xkcd.com/comics/real_programmers.png",
        "http://dilbert.com/dyn/str_strip/000000000/00000000/0000000/000000/20000/1000/000/21021/21021.strip.gif",
        "http://i50.photobucket.com/albums/f324/ann1024/DevelopersAreBornBrave%5FSmall.jpg",
        "http://imgs.xkcd.com/comics/python.png",
        "http://img34.imageshack.us/img34/9153/thenamiracleoccurscarto.png",
        "http://i35.tinypic.com/fnd343.jpg",
        "http://imgs.xkcd.com/comics/lisp_cycles.png",
        "http://dilbert.com/dyn/str_strip/000000000/00000000/0000000/000000/00000/1000/000/1051/1051.strip.gif",
        "http://imgs.xkcd.com/comics/lisp.jpg",
        "http://img128.imageshack.us/img128/1553/24383stripme5.gif",
        "http://img715.imageshack.us/img715/4185/base10z.png",
        "http://imgs.xkcd.com/comics/pointers.png",
        "http://imgs.xkcd.com/comics/estimation.png",
        "http://imgs.xkcd.com/comics/zealous_autoconfig.png",
        "http://img111.imageshack.us/img111/7577/dilbert2733310071001kc4.gif",
        "http://imgs.xkcd.com/comics/listen_to_yourself.png",
        "http://www.phdcomics.com/comics/archive/phd113007s.gif",
        "http://imgs.xkcd.com/comics/travelling_salesman_problem.png",
        "http://imgs.xkcd.com/comics/windows_7.png",
        "http://dilbert.com/dyn/str_strip/000000000/00000000/0000000/000000/00000/0000/100/170/170.strip.gif",
        "http://www.mscha.net/tmp/dt19920908.gif",
        "http://imgs.xkcd.com/comics/compiler_complaint.png",
        "http://www.dilbert.com/dyn/str%5Fstrip/000000000/00000000/0000000/000000/00000/1000/100/1149/1149.strip.gif",
        "http://www.brainstuck.com/wp-content/uploads/2008/08/t-and-c-carefully-400x376.jpg",
        "http://img357.imageshack.us/img357/1883/12563stripgw6.gif",
        "http://imgs.xkcd.com/comics/nerd_sniping.png",
        "http://imgs.xkcd.com/comics/frustration.png",
        "http://imgs.xkcd.com/comics/cant_sleep.png",
        "http://www.ibiblio.org/Dave/Dr-Fun/df200002/df20000210.jpg",
        "http://imgs.xkcd.com/comics/cnr.png",
        "http://www.dilbert.com/dyn/str_strip/000000000/00000000/0000000/000000/00000/0000/000/47/47.strip.sunday.gif",
        "http://imgs.xkcd.com/comics/flow%5Fcharts.png",
        "http://imgs.xkcd.com/comics/how%5Fit%5Fworks.png",
        "http://i89.photobucket.com/albums/k220/kipper_308/fran_web.jpg",
        "http://imgs.xkcd.com/comics/success.png",
        "http://i287.photobucket.com/albums/ll158/PhilHaney/23872stripsunday.gif",
        "http://dilbert.com/dyn/str%5Fstrip/000000000/00000000/0000000/000000/10000/5000/000/15063/15063.strip.gif",
        "http://imgs.xkcd.com/comics/im_an_idiot.png",
        "http://www.codecomics.com/images/comics/assalto_en.png",
        "http://codeclimber.net.nz/images/codeclimber_net_nz/WindowsLiveWriter/c506f37540e1.NETvsPHPintheEnterprise_AB36/DotNetPhpCartoon01_3.png",
        "http://img261.imageshack.us/img261/7469/1517stripsundayiu1.gif",
        "http://sontag.ca/gif/dilbertQuickProtect.gif",
        "http://i18.tinypic.com/34461wi.jpg",
        "http://www.phdcomics.com/comics/archive/phd120804s.gif",
        "http://lbrandy.com/blog/wp-content/uploads/2008/10/jpg_vs_png2.png",
        "http://dilbert.com/dyn/str%5Fstrip/000000000/00000000/0000000/000000/00000/1000/800/1890/1890.strip.gif",
        "http://img.thedailywtf.com/images/200905/errord/previouserror.png",
        "http://imgur.com/uhudo.jpg",
        "http://farm4.static.flickr.com/3609/3629069606%5Fa72bf52c22%5Fo.jpg",
        "http://imgs.xkcd.com/comics/pwned.png",
        "http://dilbert.com/dyn/str%5Fstrip/000000000/00000000/0000000/000000/00000/0000/200/268/268.strip.gif",
        "http://www.qwantz.com/comics/comic2-82.png",
        "http://imgs.xkcd.com/comics/security.png",
        "http://imgs.xkcd.com/comics/academia_vs_business.png",
        "http://globalnerdy.com/wordpress/wp-content/uploads/2007/11/dilbert-xp02.gif",
        "http://www.ok-cancel.com/strips/okcancel20031003.gif",
        "http://www.ok-cancel.com/strips/okcancel20031010.gif",
        "http://arsecandle.org/~rod/foxtrot_java.JPG",
        "http://dilbert.com/dyn/str_strip/000000000/00000000/0000000/000000/10000/0000/700/10797/10797.strip.gif",
        "http://www.unt.edu/benchmarks/archives/2004/february04/screwupcolor.gif",
        "http://imgs.xkcd.com/comics/road%5Frage.png",
        "http://www.overcompensating.com/comics/20080207.png",
        "http://i.stack.imgur.com/INdGC.jpg",
        "http://www.lore.ua.ac.be/Teaching/SE3BAC/SoftwareSpecCartoon.gif",
        "http://geekhero.iovene.com/comics/2008-08-20-Microsoft-Word.png",
        "http://www.bugbash.net/strips/bug-bash20050521.gif",
        "http://i44.tinypic.com/f07yh3.jpg",
        "http://www.phdcomics.com/comics/archive/phd101305s.gif",
        "http://imgs.xkcd.com/comics/1337%5Fpart%5F1.png",
        "http://www.cartoonstock.com/lowres/bro0048l.jpg",
        "http://www.phdcomics.com/comics/archive/phd011406s.gif",
        "http://zeljkofilipin.com/wp-content/uploads/2008/02/real-programmers-code-in-binary.jpg",
        "http://imgs.xkcd.com/comics/not_enough_work.png",
        "http://www.jpaulmorrison.com/fbp/State%5Fof%5Fthe%5Fart.jpg",
        "http://img111.imageshack.us/img111/9510/83482523kd2.png",
        "http://farm4.static.flickr.com/3048/2864970757_2fd6d06367.jpg?v=0",
        "http://www.geekherocomic.com/comics/2009-07-29-two-questions.png",
        "http://www.geekculture.com/joyoftech/joyimages/1151real.jpg",
        "http://imgs.xkcd.com/comics/e%5Fto%5Fthe%5Fpi%5Fminus%5Fpi.png",
        "http://www.saturngod.net/wp-content/uploads/linuxevil.png",
        "http://www.userfriendly.org/cartoons/archives/08jun/uf011627.gif",
        "http://www.phdcomics.com/comics/archive/phd012609s.gif",
        "http://pascalg.files.wordpress.com/2007/06/software-outsourcing-cartoon-3.jpg",
        "http://amoebarepublic.files.wordpress.com/2008/04/programmer.gif",
        "http://www.userfriendly.org/cartoons/archives/07sep/uf010710.gif",
        "http://wondermark.com/comics/190.gif",
        "http://sontag.ca/gif/dilbertPairProgramming.gif",
        "http://plg.uwaterloo.ca/~migod/fun/dilbert-reqs.gif",
        "http://www.dilbert.com/dyn/str_strip/000000000/00000000/0000000/000000/10000/2000/400/12456/12456.strip.print.gif",
        "http://imgs.xkcd.com/comics/a_bunch_of_rocks.png",
        "http://www.arcanology.net/sticksandstones/comics/comic-10.gif",
        "http://www.qwantz.com/comics/comic2-172.png",
        "http://img-fotki.yandex.ru/get/3502/jim1537.52/0%5F2805a%5F2b9e39ea%5Forig",
        "http://img17.imageshack.us/img17/3108/bananaadgz4.gif",
        "http://ihooky.com/comics/ihooky06.png",
        "http://imgs.xkcd.com/comics/new_pet.png",
        "http://imgs.xkcd.com/comics/donald_knuth.png",
        "http://wondermark.com/comics/352.gif",
        "http://www.dilbert.com/dyn/str%5Fstrip/000000000/00000000/0000000/000000/00000/6000/700/6783/6783.strip.gif",
    );

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'irc.received.PRIVMSG' => array('onMessage', 0)
        );
    }

    /**
     * @param Event $event
     */
    public function onMessage(Event $event)
    {
        $message = $event->getMessage();
        $matches = array();

        if ($message->isInChannel() && preg_match(self::TRIGGER_PATTERN, $message['text'], $matches)) {
            $event->getWriteStream()->ircPrivmsg(
                $message->getSource(),
                sprintf(
                    self::MESSAGE,
                    $this->cartoons[rand(0, count($this->cartoons) - 1)]
                )
            );
        }
    }
}

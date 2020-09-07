<?php

class Additional
{
    public function __construct(General $site)
    {
        foreach ($site as $item => $val) {
            $this->$item = $val;
        }
    }

    public function clean_comments(&$view)
    {
        $start = explode("<!-", $view);
        foreach ($start as $st) {
            $end = explode("-->", $st);
            if (isset($end['1'])) {
                $line = "<!-{$end['0']}-->";
                $view = str_replace($line, '', $view);
            }
        }
        $this->right_breaks($view);
        $this->remove_double_breaks($view);
    }

    public function right_breaks(&$view)
    {
        $view = str_replace("\r\n", "\n", $view);
    }

    public function remove_double_breaks(&$view)
    {
        $view = str_replace("\n\n","\n",$view);
    }

    public function random_fake_div($array)
    {
        $count = count($array) - 1;
        $div = $array[rand(0, $count)];
        $random_text = '';
        for ($i = 0; $i < rand(10, 50); $i++) {
            $random_text .= $this->generate_random_string(rand(3,60)) . ' ';
        }
        $random_classes = '';
        for ($i = 0; $i < rand(1, 10); $i++) {
            $random_classes .= $this->generate_random_string(rand(5,10)) . ' ';
        }
        $random_text = ucfirst(strtolower(trim($random_text, ' ')));
        $random_classes = trim($random_classes, ' ');
        $div = str_replace('{random_text}', $random_text, $div);
        $div = str_replace('{random_classes}', $random_classes, $div);
        return $div;
    }

    public function fake_divs(&$data)
    {
        $array = [
            '<div class="{random_classes}" style="opacity:0;z-index:-999;pointer-events:none;position:absolute;display:none;">{random_text}</div>',
            '<div class="{random_classes}" style="opacity:0;display:none;">{random_text}</div>',
            '<div class="{random_classes}" style="opacity:0;pointer-events:none;display:none;position:absolute;top:-1231px;left:-9999px">{random_text}</div>',
        ];
        
        $bodySt = explode("<body",$data);
        $bodyIn = explode("</body",$bodySt['1']);
        $body = trim($bodyIn['0'], ' ><\/');
        $divOut = explode("</div>", $body);
        foreach ($divOut as $divq) {
            $bodyNew[] = $divq . '</div>' . $this->random_fake_div($array);
        }
        $bodyNew = implode("", $bodyNew);
        $data = str_replace($body, $bodyNew, $data);
    }

    public function generate_random_string($val = 12)
    {
        // val - количество символов. строка ограничена только буквами английского алфавита
        $alphas = array_merge(range('a', 'z'), range('A', 'Z'));
        $str = '';
        for ($i = 0; $i < $val; $i++) {
            $str .= $alphas[rand(0, count($alphas) - 1)];
        }
        return $str;
    }

    public function fake_classes(&$data)
    {
        $from = [];
        $classes = explode("class=\"", $data);
        array_shift($classes);
        foreach ($classes as $class) {
            $names = explode("\"", $class);
            $from[] = $names['0'];
        }
        foreach ($from as $cl) {
            $array = [];
            $clq = [];
            $clx = explode(" ", $cl);
            foreach ($clx as $clc) {
                $new = $this->generate_random_string(rand(5,50));
                $newb = $this->generate_random_string(rand(5,50));
                $clq[] = $new . ' ' . $clc . ' ' . $newb;
            }
            $clq = implode(" ", $clq);
            $array['from'] = 'class="' . $cl . '"';
            $array['to'] = 'class="' . $clq . '"';
            $rename[] = $array;
        }
        if (!empty($rename)) {
            foreach ($rename as $act) {
                $data = str_replace($act['from'], $act['to'], $data);
            }
        }
    }

    public function remove_breaks(&$view)
    {
        for ($i = 0; $i < 20; $i++) {
            $view = str_replace("\n", " ", $view);
            $view = str_replace("  ", " ", $view);
            $view = str_replace("> <", "><", $view);
        }
    }

    public function clean_phone_form(&$view)
    {
        $view = str_replace(' name="phone_number-1"', ' name="phone_number"', $view);
    }
}
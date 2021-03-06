class JavaLangStringTest
{
    public static void charAtIndex(String a, int b)
    {
        System.out.print(a.charAt(b));
    }

    public static void concat(String a, String b)
    {
        System.out.print(a.concat(b));
    }

    public static void testHashCode()
    {
        System.out.println("hello, world".hashCode());
        System.out.println("HELLO, WORLD".toLowerCase().hashCode());
        System.out.println((new String("hello, world")).hashCode());
    }

    public static void intern()
    {
        String te = "te";
        String st = "st";

        String test = te + st;
        System.out.println(System.identityHashCode(test));

        test.intern();
        System.out.println(System.identityHashCode("test"));
    }

    public static void notInterned()
    {
        String te = "te";
        String st = "st";

        String test = te + st;
        System.out.println(System.identityHashCode(test));

        System.out.println(System.identityHashCode("test"));
    }

    public static void notInternedAfterLiteral()
    {
        String te = "te";
        String st = "st";

        String test = te + st;
        System.out.println(System.identityHashCode(test));
        System.out.println(System.identityHashCode("test"));

        test.intern();
        System.out.println(System.identityHashCode("test"));
    }

    public static void replace(String a, String b, String c)
    {
        System.out.print(a.replace(b, c));
    }

    public static void toLowerCase(String a)
    {
        System.out.print(a.toLowerCase());
    }

    public static void toUpperCase(String a)
    {
        System.out.print(a.toUpperCase());
    }
}
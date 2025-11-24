@props([
  'subheading' => 'Subheading',
  'heading' => 'Heading',
])

<div class="flex flex-col bg-bg-base-default border border-bor-base-default rounded-lg shadow-sm h-full min-h-0">
    <div class="flex flex-col px-4 pt-2 pb-1">
        <p class="text-sm font-normal text-txt-base-secondary">{{ $subheading }}</p>
        <h1 class="text-base font-semibold text-acc-base-default">{{ $heading }}</h1>
    </div>

    <div class="flex flex-col flex-1 gap-2 min-h-0">
        <!-- Table Header -->
        <table class="table-fixed w-full">
            <thead>
                <tr class="flex flex-row flex-1 p-4 gap-4 border-b border-bor-base-default">
                    <th class="w-full text-justify text-base font-semibold">Table Header</th>
                    <th class="w-full text-justify text-base font-semibold">Table Header</th>
                    <th class="w-full text-justify text-base font-semibold">Table Header</th>
                </tr>
            </thead>
        </table>

        <!-- Table Body -->
        <div class="flex-1 min-h-0 overflow-y-auto">
            <table class="table-fixed w-full">
                <tbody>
                    <tr class="flex flex-row flex-1 p-4 gap-4 border-b border-bor-base-default">
                        <td class="w-full text-justify text-base font-normal">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Voluptatibus,
                            veniam!</td>
                        <td class="w-full text-justify text-base font-normal">Lorem ipsum dolor sit amet consectetur adipisicing elit. Non quidem ipsa
                            ducimus ipsum, maxime cumque facilis dolor commodi, perferendis aut deleniti. Et, molestiae
                            consequatur. Vitae.</td>
                        <td class="w-full text-justify text-base font-normal">Lorem ipsum dolor sit amet consectetur adipisicing elit. Repellat odio
                            doloremque temporibus accusantium ducimus non dolore perspiciatis distinctio enim
                            exercitationem? Eaque, repudiandae. Sunt, ullam saepe!</td>
                    </tr>
                    <tr class="flex flex-row flex-1 p-4 gap-4 border-b border-bor-base-default">
                        <td class="w-full text-justify text-base font-normal">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Voluptatibus,
                            veniam!</td>
                        <td class="w-full text-justify text-base font-normal">Lorem ipsum dolor sit amet consectetur adipisicing elit. Non quidem ipsa
                            ducimus ipsum, maxime cumque facilis dolor commodi, perferendis aut deleniti. Et, molestiae
                            consequatur. Vitae.</td>
                        <td class="w-full text-justify text-base font-normal">Lorem ipsum dolor sit amet consectetur adipisicing elit. Repellat odio
                            doloremque temporibus accusantium ducimus non dolore perspiciatis distinctio enim
                            exercitationem? Eaque, repudiandae. Sunt, ullam saepe!</td>
                    </tr>
                    <tr class="flex flex-row flex-1 p-4 gap-4 border-b border-bor-base-default">
                        <td class="w-full text-justify text-base font-normal">Lorem, ipsum dolor sit amet consectetur adipisicing elit. Voluptatibus,
                            veniam!</td>
                        <td class="w-full text-justify text-base font-normal">Lorem ipsum dolor sit amet consectetur adipisicing elit. Non quidem ipsa
                            ducimus ipsum, maxime cumque facilis dolor commodi, perferendis aut deleniti. Et, molestiae
                            consequatur. Vitae.</td>
                        <td class="w-full text-justify text-base font-normal">Lorem ipsum dolor sit amet consectetur adipisicing elit. Repellat odio
                            doloremque temporibus accusantium ducimus non dolore perspiciatis distinctio enim
                            exercitationem? Eaque, repudiandae. Sunt, ullam saepe!</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

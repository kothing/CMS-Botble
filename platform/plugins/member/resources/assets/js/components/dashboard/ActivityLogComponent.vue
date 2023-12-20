<template>
    <div class="bg-white br2 pa3" style="box-shadow: 0 1px 1px #d9d9d9">
        <ul class="nav" role="tablist" style="border-bottom: 1px solid #d9d9d9">
            <li class="nav-item gray-text">
                <a
                    @click.prevent="setActiveTab(activityLogTab)"
                    :class="[activeTab === activityLogTab ? 'show active b' : '']"
                    class="no-underline mr2 black-50 hover-black-70 pv1 ph2 db"
                    style="text-decoration: none; line-height: 32px"
                    data-toggle="tab"
                    :href="'#' + activityLogTab"
                    >{{ __('activity_logs') }}</a
                >
            </li>
        </ul>

        <div class="tab-content">
            <div
                :class="[activeTab === activityLogTab ? 'tab-pane fade show active' : 'tab-pane fade']"
                :id="activityLogTab"
            >
                <!--Loading spinner-->
                <div v-if="isLoading" class="tc pv3">
                    <div class="m-auto" style="width: 20px">
                        <half-circle-spinner :animation-duration="1000" :size="15" color="#808080" />
                    </div>
                </div>

                <div v-if="!isLoading && activityLogs.meta.total === 0" class="tc light-gray-text pa3">
                    <div class="f5 mv2 b">{{ __('oops') }}</div>
                    <div class="f6 mv2">{{ __('no_activity_logs') }}</div>
                </div>

                <div v-if="!isLoading && activityLogs.meta.total !== 0" class="pv3 ph3-ns">
                    <div v-for="activityLog in activityLogs.data" :key="activityLog.id" class="mb3">
                        <div class="dib light-gray-text">
                            <i class="far fa-clock mr2"></i>
                            <span
                                :title="$sanitize(activityLog.description, { allowedTags: [] })"
                                v-html="$sanitize(activityLog.description)"
                            ></span>
                            <span
                                >(<a
                                    :href="'https://whatismyipaddress.com/ip/' + activityLog.ip_address"
                                    target="_blank"
                                    :title="activityLog.ip_address"
                                    >{{ activityLog.ip_address }}</a
                                >)</span
                            >
                        </div>
                    </div>
                </div>

                <div v-if="!isLoading && activityLogs.links.next" class="tc light-gray-text">
                    <a
                        class="f6 mv2"
                        href="javascript:void(0)"
                        v-if="!isLoadingMore"
                        @click="getActivityLogs(activityLogs.links.next)"
                        >{{ __('load_more') }}</a
                    >
                    <a class="f6 mv2" href="javascript:void(0)" v-if="isLoadingMore">{{ __('loading_more') }}</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { HalfCircleSpinner } from 'epic-spinners'

export default {
    components: {
        HalfCircleSpinner,
    },

    data() {
        return {
            activeTab: '',
            activityLogTab: 'activity-logs',
            isLoading: true,
            isLoadingMore: false,
            activityLogs: [],
        }
    },

    mounted() {
        this.prepareComponent()
    },

    props: ['defaultActiveTab'],

    methods: {
        prepareComponent() {
            this.setActiveTab(this.defaultActiveTab)
        },

        setActiveTab(tab) {
            if (this.activeTab === tab) {
                return
            }

            this.activeTab = tab

            this.reloadData()
        },

        reloadData() {
            if (this.activeTab === this.activityLogTab) {
                return this.getActivityLogs()
            }

            return []
        },

        getActivityLogs(url = null) {
            if (url) {
                this.isLoadingMore = true
            } else {
                this.isLoading = true
            }
            axios.get(url ? url : '/ajax/members/activity-logs').then((res) => {
                let oldData = []
                if (this.activityLogs.data) {
                    oldData = this.activityLogs.data
                }
                this.activityLogs = res.data
                this.activityLogs.data = oldData.concat(this.activityLogs.data)
                this.isLoading = false
                this.isLoadingMore = false
            })
        },
    },
}
</script>
